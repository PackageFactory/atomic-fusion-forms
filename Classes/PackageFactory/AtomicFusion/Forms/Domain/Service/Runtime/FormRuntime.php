<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime;

/**
 * This file is part of the PackageFactory.AtomicFusion.Forms package
 *
 * (c) 2016 Wilhelm Behncke <wilhelm.behncke@googlemail.com>
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Property\PropertyMappingConfiguration;
use TYPO3\Flow\Utility\Arrays;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Factory\FormStateFactory;
use PackageFactory\AtomicFusion\Forms\Factory\PropertyMappingConfigurationFactory;

/**
 * The form runtime
 */
class FormRuntime
{
	/**
	 * @var ActionRequest
	 */
	protected $request;

    /**
     * @var FormStateInterface
     */
    protected $formState;

    /**
     * @var FormDefinitionInterface
     */
    protected $formDefinition;

    /**
	 * @var array
	 */
	protected $arguments;

    /**
	 * @var array
	 */
	protected $values;

    /**
     * @Flow\Inject
	 * @var Result
	 */
	protected $validationResult;

	/**
	 * @Flow\Inject
	 * @var Tasks\ProcessTaskInterface
	 */
	protected $processTask;

	/**
	 * @Flow\Inject
	 * @var Tasks\ValidateTaskInterface
	 */
	protected $validateTask;

	/**
	 * @Flow\Inject
	 * @var Tasks\RollbackTaskInterface
	 */
	protected $rollbackTask;

	/**
	 * @Flow\Inject
	 * @var Tasks\FinishTaskInterface
	 */
	protected $finishTask;

	/**
     * @var PropertyMappingConfiguration
     */
    protected $propertyMappingConfiguration;

	/**
	 * @Flow\Inject
     * @var PropertyMappingConfigurationFactory
     */
    protected $propertyMappingConfigurationFactory;

	/**
	 * @Flow\Inject
     * @var FormStateFactory
     */
    protected $formStateFactory;

    /**
     * Constructor
     *
     * @param FormDefinitionInterface $formDefinition
     * @param ActionRequest $request
     */
    public function __construct(FormDefinitionInterface $formDefinition, ActionRequest $request)
    {
        $this->formDefinition = $formDefinition;

        //
		// Create sub request
		//
		$rootRequest = $request->getMainRequest() ?: $request;
        $pluginArguments = $rootRequest->getPluginArguments();

        $this->request = new ActionRequest($request);
        $this->request->setArgumentNamespace('--' . $this->formDefinition->getName());

        if (isset($pluginArguments[$this->formDefinition->getName()])) {
            $this->request->setArguments($pluginArguments[$this->formDefinition->getName()]);
        }
    }

    /**
	 * Restore or initialize form state, create property mapping configuration
	 *
	 * @return void
	 */
	protected function initializeObject()
	{
		$this->formState = $this->formStateFactory->createFromActionRequest($this->request);

		$this->arguments = Arrays::arrayMergeRecursiveOverrule(
			$this->formState->getArguments(),
			$this->request->getArguments()
		);

        $this->propertyMappingConfiguration = $this->propertyMappingConfigurationFactory
			->createTrustedPropertyMappingConfiguration(
				$this->request->getInternalArgument('__trustedProperties')
			);
	}

    /**
     * @inheritdoc
     */
    public function getFormDefinition()
    {
        return $this->formDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @inheritdoc
     */
    public function getFormState()
    {
        return $this->formState;
    }

    /**
     * @inheritdoc
     */
    public function process()
    {
        $fieldDefinitions = $this->getFieldDefinitionsForCurrentPage();

        $this->values = [];
        foreach ($fieldDefinitions as $fieldDefinition) {
			$input = null;

			if (array_key_exists($fieldDefinition->getName(), $this->arguments)) {
				$input = $this->arguments[$fieldDefinition->getName()];
			}

			$this->values[$fieldDefinition->getName()] = $this->processTask
				->run($this->propertyMappingConfiguration, $fieldDefinition, $input, $this->validationResult);
        }
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        $fieldDefinitions = $this->getFieldDefinitionsForCurrentPage();

        foreach ($fieldDefinitions as $fieldDefinition) {
            $value = null;

			if (array_key_exists($fieldDefinition->getName(), $this->values)) {
				$value = $this->values[$fieldDefinition->getName()];
			}

			$this->validateTask->run($fieldDefinition, $value, $this->validationResult);
        }
    }

    /**
     * @inheritdoc
     */
    public function rollback()
    {
		$fieldDefinitions = $this->getFieldDefinitionsForCurrentPage();

        foreach ($fieldDefinitions as $fieldDefinition) {
			$input = null;
			if (array_key_exists($fieldDefinition->getName(), $this->arguments)) {
				$input = $this->arguments[$fieldDefinition->getName()];
			}

			$value = null;
			if (array_key_exists($fieldDefinition->getName(), $this->values)) {
				$value = $this->values[$fieldDefinition->getName()];
			}

			$this->values[$fieldDefinition->getName()] = $this->rollbackTask
				->run($this->propertyMappingConfiguration, $fieldDefinition, $input, $value, $this->validationResult);
        }
    }

    /**
     * @inheritdoc
     */
    public function finish(Response $parentResponse)
    {
        $finisherDefinitions = $this->formDefinition->getFinisherDefinitions();

        return $this->finishTask->run($finisherDefinitions, $parentResponse);
    }

    /**
     * Get the field definitions for the current page
     *
     * @return array<FieldDefinitionInterface>
     */
    protected function getFieldDefinitionsForCurrentPage()
    {
        if ($this->formDefinition->hasPages()) {
            return $this->formDefinition
                ->getPageDefinition($this->formState->getCurrentPage())
                ->getFieldDefinitions();
        }

		return $this->formDefinition->getFieldDefinitions();
    }
}
