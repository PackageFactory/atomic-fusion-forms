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

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Error\Result;
use Neos\Flow\Http\Response;
use Neos\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\Factory\FormStateFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormStateInterface;
use PackageFactory\AtomicFusion\Forms\Factory\PropertyMappingConfigurationFactory;

/**
 * The form runtime
 */
class FormRuntime implements FormRuntimeInterface
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
	 * @Flow\Inject
	 * @var Task\ProcessTaskInterface
	 */
	protected $processTask;

	/**
	 * @Flow\Inject
	 * @var Task\ValidateTaskInterface
	 */
	protected $validateTask;

	/**
	 * @Flow\Inject
	 * @var Task\RollbackTaskInterface
	 */
	protected $rollbackTask;

	/**
	 * @Flow\Inject
	 * @var Task\FinishTaskInterface
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
		$this->formState->mergeArguments($this->request->getArguments());

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
	public function shouldProcess()
	{
		return !$this->formState->isInitialCall();
	}

    /**
     * @inheritdoc
     */
    public function process()
    {
        $fieldDefinitions = $this->getFieldDefinitionsForCurrentPage();

        $this->values = [];
        foreach ($fieldDefinitions as $fieldDefinition) {
            $argument = $this->formState->getArgument($fieldDefinition->getName());

			$value = $this->processTask->run(
				$this->propertyMappingConfiguration,
				$fieldDefinition,
				$argument,
				$this->formState->getValidationResult()
			);

			$this->formState->addValue($fieldDefinition->getName(), $value);
        }
    }

	/**
     * @inheritdoc
     */
	public function shouldValidate()
	{
		return !$this->formState->isInitialCall() && count($this->formState->getValues()) > 0;
	}

    /**
     * @inheritdoc
     */
    public function validate()
    {
        $fieldDefinitions = $this->getFieldDefinitionsForCurrentPage();

        foreach ($fieldDefinitions as $fieldDefinition) {
            $value = $this->formState->getValue($fieldDefinition->getName());
			$this->validateTask->run($fieldDefinition, $value, $this->formState->getValidationResult());
        }
    }

	/**
     * @inheritdoc
     */
	public function shouldRollback()
	{
		return $this->formState->getValidationResult()->hasErrors();
	}

    /**
     * @inheritdoc
     */
    public function rollback()
    {
		$fieldDefinitions = $this->getFieldDefinitionsForCurrentPage();

        foreach ($fieldDefinitions as $fieldDefinition) {
			$argument = $this->formState->getArgument($fieldDefinition->getName());
			$value = $this->formState->getValue($fieldDefinition->getName());

			$restoredValue = $this->rollbackTask
				->run(
					$this->propertyMappingConfiguration,
					$fieldDefinition,
					$argument,
					$value,
					$this->formState->getValidationResult()
				);

			$this->formState->addValue($fieldDefinition->getName(), $restoredValue);
        }
    }

	/**
     * @inheritdoc
     */
	public function shouldFinish()
	{
		$pageDefinitions = $this->formDefinition->getPageDefinitions();
		$isOnLastPage = false;

		if (is_array($pageDefinitions)) {
			$lastPageDefinition = array_pop($pageDefinitions);
			if ($lastPageDefinition) {
				$isOnLastPage = $this->formState->getCurrentPage() === $lastPageDefinition->getName();
			}
		}

		return !$this->formState->isInitialCall() && !$this->formState->getValidationResult()->hasErrors() && (
			!$this->formDefinition->hasPages() || $isOnLastPage
		);
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
