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
use TYPO3\TypoScript\TypoScriptObjects\AbstractArrayTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Service\FormContext;
use PackageFactory\AtomicFusion\Forms\Service\FormAugmentationService;
use PackageFactory\AtomicFusion\Forms\Service\HiddenInputTagMappingService;

class FormImplementation extends AbstractArrayTypoScriptObject
{
	const CONTEXT_IDENTIFIER_FORMCONTEXT = '@@' . self::class . ':formContext';

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

	public function evaluate()
	{
		//
		// Create Form context with subrequest
		//
		$request = $this->tsRuntime->getControllerContext()->getRequest();
		$formContext = new FormContext($this->path, $this->properties, $request);

		//
		// Render
		//
		$this->tsRuntime->pushContextArray([
			self::CONTEXT_IDENTIFIER_FORMCONTEXT => $formContext
		]);
		$renderedForm = $this->tsRuntime->render(sprintf('%s/renderer', $this->path));
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
					'__state' => $formContext->getEncodedFormState()
				], $formContext->getArgumentNamespace())
			)
		);
	}
}
