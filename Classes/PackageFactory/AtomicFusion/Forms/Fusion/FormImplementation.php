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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Context\FormContext;
use PackageFactory\AtomicFusion\Forms\Service\CryptographyService;
use PackageFactory\AtomicFusion\Forms\Service\FormAugmentationService;
use PackageFactory\AtomicFusion\Forms\Service\HiddenInputTagMappingService;
use PackageFactory\AtomicFusion\Forms\Service\PropertyMappingConfigurationService;

class FormImplementation extends AbstractTypoScriptObject
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
	 * Create a form definition from the current fusion configuration
	 *
	 * @return FormDefinitionInterface
	 */
	public function getFormDefinition()
	{
		$fields = $this->tsValue('fields');
		$finishers = $this->tsValue('finishers');
		$pages = $this->tsValue('pages');

		$formDefinition = new FormDefinition([
			'label' => $this->tsValue('label'),
			'name' => $this->tsValue('name'),
			'action' => $this->tsValue('action')
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

		return $formDefinition;
	}

	public function getFormRuntime()
	{
		$formDefinition = $this->getFormDefinition();
		$request = $this->tsRuntime->getControllerContext()->getRequest();

		return new FormRuntime($formDefinition, $request);
	}

	public function evaluate()
	{
		//
		// Create form definition
		//
		$formRuntime = $this->getFormRuntime();
		$formContext = new FormContext($formRuntime);

		$this->tsRuntime->pushContextArray($this->tsRuntime->getCurrentContext() + [
			$this->tsValue('formContext') => $formContext
		]);

		if ($formRuntime->shouldProcess()) {
			$formRuntime->process();

			if ($formRuntime->shouldValidate()) {
				$formRuntime->validate();

				if ($formRuntime->shouldRollback()) {
					$formRuntime->rollback();
				} else if ($formRuntime->shouldFinish()) {
					$controllerContext = $this->tsRuntime->getControllerContext();
					$finisherState = $formRuntime->finish($controllerContext->getResponse());
					$response = $finisherState->getResponse();

					if ($statusCode = $response->getStatusCode()) {
						$controllerContext->getResponse()->setStatus($statusCode);
					}

					if ($flashMessages = $finisherState->getFlashMessageContainer()->getMessagesAndFlush()) {
						foreach ($flashMessages as $flashMessage) {
							$controllerContext->getFlashMessageContainer()->addMessage($flashMessage);
						}
					}

					if ($content = $response->getContent()) {
						$this->tsRuntime->popContext();
						return $content;
					}
				}
			}
		}

		//
		// Render
		//
		if ($formRuntime->getFormDefinition()->hasPages()) {
			$currentPage = $formRuntime->getFormState()->getCurrentPage();
			$nextPage = $currentPage;

			if (!$formRuntime->getFormState()->getValidationResult()->hasErrors()) {
				$nextPage = $formRuntime->getFormDefinition()->getNextPage($currentPage);
			}

			$formRuntime->getFormState()->setCurrentPage($nextPage);

			$this->tsRuntime->pushContextArray($this->tsRuntime->getCurrentContext() + [
				$this->tsValue('pageContext') => $formContext->page($nextPage)
			]);

			$renderedForm = $this->tsRuntime->render(sprintf('%s/renderer/%s', $this->path, $nextPage));

			$this->tsRuntime->popContext();
		} else {
			$renderedForm = $this->tsRuntime->render(sprintf('%s/renderer', $this->path));
		}

		$this->tsRuntime->popContext();

		//
		// Augment rendering result with form meta information
		//
		// - Form State
		// - Trusted Properties
		//
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
