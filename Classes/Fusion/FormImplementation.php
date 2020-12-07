<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion;

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
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Factory\FormRuntimeFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\FormDefinitionFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Context\FormContext;
use PackageFactory\AtomicFusion\Forms\Domain\Context\Factory\FormContextFactory;
use PackageFactory\AtomicFusion\Forms\Service\CryptographyService;
use PackageFactory\AtomicFusion\Forms\Service\FormAugmentationService;
use PackageFactory\AtomicFusion\Forms\Service\HiddenInputTagMappingService;
use PackageFactory\AtomicFusion\Forms\Service\PropertyMappingConfigurationService;

class FormImplementation extends AbstractFusionObject
{
    /**
     * @Flow\Inject
     * @var CryptographyService
     */
    protected $cryptographyService;

    /**
     * @Flow\Inject
     * @var FormAugmentationService
     */
    protected $formAugmentationService;

    /**
     * @Flow\Inject
     * @var HiddenInputTagMappingService
     */
    protected $hiddenInputTagMappingService;

    /**
     * @Flow\Inject
     * @var PropertyMappingConfigurationService
     */
    protected $propertyMappingConfigurationService;

    /**
     * @Flow\Inject
     * @var FormDefinitionFactory
     */
    protected $formDefinitionFactory;

    /**
     * @Flow\Inject
     * @var FormRuntimeFactory
     */
    protected $formRuntimeFactory;

    /**
     * @var FormDefinitionInterface
     */
    protected $formDefinition;

    /**
     * @var FormRuntimeInterface
     */
    protected $formRuntime;

    /**
     * Create a form definition from the current fusion configuration
     *
     * @return FormDefinitionInterface
     */
    public function getFormDefinition()
    {
        if ($this->formDefinition) {
            return $this->formDefinition;
        }

        $fields = $this->fusionValue('fields');
        $finishers = $this->fusionValue('finishers');
        $pages = $this->fusionValue('pages');

        $formDefinition = $this->formDefinitionFactory->createFormDefinition([
            'label' => $this->fusionValue('label'),
            'name' => $this->fusionValue('name'),
            'action' => $this->fusionValue('action')
        ]);

        foreach ($fields as $field) {
            $field->setFormDefinition($formDefinition);
            $formDefinition->addFieldDefinition($field);
        }

        foreach ($finishers as $finisher) {
            $formDefinition->addFinisherDefinition($finisher);
        }

        foreach ($pages as $page) {
            $page->setFormDefinition($formDefinition);
            $formDefinition->addPageDefinition($page);
        }

        return $this->formDefinition = $formDefinition;
    }

    /**
     * Create a new form runtime from the current fusion configuration and the current
     * action request
     *
     * @return FormRuntimeInterface
     */
    public function getFormRuntime()
    {
        if ($this->formRuntime) {
            return $this->formRuntime;
        }

        $formDefinition = $this->getFormDefinition();
        $request = $this->runtime->getControllerContext()->getRequest();

        return $this->formRuntime = $this->formRuntimeFactory->createFormRuntime($formDefinition, $request);
    }

    public function evaluate()
    {
        //
        // Create form definition
        //
        $formRuntime = $this->getFormRuntime();
        $formContext = $formRuntime->getFormContext();

        $this->runtime->pushContext($this->fusionValue('formContext'), $formContext);

        $renderedForm = $this->processForm($formRuntime);
        $renderedForm = $renderedForm ? $renderedForm : $this->augmentForm(
            $this->renderForm($formRuntime, $formContext),
            $formRuntime,
            $formContext
        );
        $this->runtime->popContext();

        return $renderedForm;
    }

    /**
     * Perform runtime tasks on current form state
     *
     * @param FormRuntimeInterface $formRuntime
     * @return string|null
     */
    public function processForm(FormRuntimeInterface $formRuntime)
    {
        if ($formRuntime->shouldProcess()) {
            $formRuntime->process();

            if ($formRuntime->shouldValidate()) {
                $formRuntime->validate();

                if ($formRuntime->shouldRollback()) {
                    $formRuntime->rollback();
                } elseif ($formRuntime->shouldFinish()) {
                    $controllerContext = $this->runtime->getControllerContext();
                    $finisherState = $formRuntime->finish($controllerContext->getResponse());
                    $response = $finisherState->getResponse();

                    if ($statusCode = $response->getStatusCode()) {
                        $controllerContext->getResponse()->setStatusCode($statusCode);
                    }

                    if ($flashMessages = $finisherState->getFlashMessageContainer()->getMessagesAndFlush()) {
                        foreach ($flashMessages as $flashMessage) {
                            $controllerContext->getFlashMessageContainer()->addMessage($flashMessage);
                        }
                    }

                    if ($content = $response->getContent()) {
                        return $content;
                    }
                }
            }
        }
    }

    /**
     * Render the form
     *
     * @param FormRuntimeInterface $formRuntime
     * @return string
     */
    public function renderForm(FormRuntimeInterface $formRuntime, FormContext $formContext)
    {
        $formDefinition = $formRuntime->getFormDefinition();
        $formState = $formRuntime->getFormState();

        if ($formDefinition->hasPages()) {
            $currentPage = $formState->getCurrentPage();
            $nextPage = $currentPage;

            if (!$formState->getValidationResult()->hasErrors()) {
                $nextPage = $formDefinition->getNextPage($currentPage);
            }

            $formState->setCurrentPage($nextPage);

            $this->runtime->pushContext($this->fusionValue('pageContext'), $formContext->page($nextPage));

            $renderedForm = $this->runtime->render(sprintf('%s/renderer/%s', $this->path, $nextPage));

            $this->runtime->popContext();
        } else {
            $renderedForm = $this->runtime->render(sprintf('%s/renderer', $this->path));
        }

        return $renderedForm;
    }

    /**
     * Augment rendering result with form meta information:
     *
     *  - Form State
     *  - Trusted Properties
     *
     * @param string $renderedForm
     * @param FormRuntimeInterface $formRuntime
     * @param FormContext $formContext
     * @return string
     */
    public function augmentForm($renderedForm, FormRuntimeInterface $formRuntime, FormContext $formContext)
    {
        return $this->formAugmentationService->injectStringAfterOpeningFormTag(
            $renderedForm,
            sprintf(
                '<div style="display: none;">%s</div>',
                $this->hiddenInputTagMappingService->convertFlatMapToHiddenInputTags([
                    '__state' => $this->cryptographyService->encodeHiddenFormMetadata(
                        $formRuntime->getFormState()
                    ),
                    '__trustedProperties' => $this->propertyMappingConfigurationService
                        ->generateTrustedPropertiesToken(
                            $formContext->getRequestedFieldNames()
                        )
                ], $formRuntime->getRequest()->getArgumentNamespace())
            )
        );
    }
}
