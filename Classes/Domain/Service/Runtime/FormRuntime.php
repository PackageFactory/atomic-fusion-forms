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
use Neos\Error\Messages\Result;
use Neos\Flow\Mvc\ActionResponse as Response;
use Neos\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Domain\Context\FormContext;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\Factory\FormStateFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Context\Factory\FormContextFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormState;
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
     * @var FormContext
     */
    protected $formContext;

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
     * @var Task\ProcessTask
     */
    protected $processTask;

    /**
     * @Flow\Inject
     * @var Task\ValidateTask
     */
    protected $validateTask;

    /**
     * @Flow\Inject
     * @var Task\RollbackTask
     */
    protected $rollbackTask;

    /**
     * @Flow\Inject
     * @var Task\FinishTask
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
     * @Flow\Inject
     * @var FormContextFactory
     */
    protected $formContextFactory;

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
        $this->formContext = $this->formContextFactory->createFormContext($this);

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
     * @return FormContext
     */
    public function getFormContext()
    {
        return $this->formContext;
    }

    /**
     * @inheritdoc
     */
    public function shouldProcess()
    {
        return $this->processTask->shouldRun($this);
    }

    /**
     * @inheritdoc
     */
    public function process()
    {
        return $this->processTask->run($this);
    }

    /**
     * @inheritdoc
     */
    public function shouldValidate()
    {
        return $this->validateTask->shouldRun($this);
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return $this->validateTask->run($this);
    }

    /**
     * @inheritdoc
     */
    public function shouldRollback()
    {
        return $this->rollbackTask->shouldRun($this);
    }

    /**
     * @inheritdoc
     */
    public function rollback()
    {
        return $this->rollbackTask->run($this);
    }

    /**
     * @inheritdoc
     */
    public function shouldFinish()
    {
        return $this->finishTask->shouldRun($this);
    }

    /**
     * @inheritdoc
     */
    public function finish(Response $parentResponse)
    {
        return $this->finishTask->run($this, $parentResponse);
    }

    /**
     * Get the field definitions for the current page
     *
     * @return array<FieldDefinitionInterface>
     */
    public function getFieldDefinitionsForCurrentPage()
    {
        if ($this->formDefinition->hasPages()) {
            return $this->formDefinition
                ->getPageDefinition($this->formState->getCurrentPage())
                ->getFieldDefinitions();
        }

        return $this->formDefinition->getFieldDefinitions();
    }

    /**
     * @return mixed
     */
    public function getPropertyMappingConfiguration()
    {
        return $this->propertyMappingConfiguration;
    }
}
