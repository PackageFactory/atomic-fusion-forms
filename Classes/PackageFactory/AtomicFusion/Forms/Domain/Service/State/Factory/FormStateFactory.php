<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\State\Factory;

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
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormState;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormStateInterface;
use PackageFactory\AtomicFusion\Forms\Service\CryptographyService;

/**
 * Create form state
 *
 * @Flow\Scope("singleton")
 */
class FormStateFactory
{
    /**
     * @Flow\Inject
     * @var CryptographyService
     */
    protected $cryptographyService;

    /**
     * Create a new form state
     *
     * @return FormStateInterface
     */
    public function createFormState()
    {
        return new FormState();
    }

    /**
     * Recreate an existing form state from a given request or create a new form state
     * if the request turns out not to contain form state data
     *
     * @return FormStateInterface
     */
    public function createFromActionRequest(ActionRequest $request)
    {
        if ($serializedFormState = $request->getInternalArgument('__state')) {
			return $this->cryptographyService->decodeHiddenFormMetadata($serializedFormState);
		}

        return $this->createFormState();
    }
}
