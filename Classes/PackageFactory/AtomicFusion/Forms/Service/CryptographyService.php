<?php
namespace PackageFactory\AtomicFusion\Forms\Service;

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
use TYPO3\Flow\Security\Cryptography\HashService;

/**
 * @Flow\Scope("singleton")
 */
class CryptographyService
{
	/**
	 * @Flow\Inject
	 * @var HashService
	 */
	protected $hashService;

	public function encodeHiddenFormMetadata($formMetadata)
	{
		$serializedFormState = base64_encode(serialize($this->arguments['object']->getFormState()));
        return $this->hashService->appendHmac($serializedFormState);
	}

	public function decodeHiddenFormMetadata($encodedHiddenFormMetadata)
	{
		$serializedFormMetadata = $this->hashService->validateAndStripHmac($encodedHiddenFormMetadata);
		return unserialize(base64_decode($serializedFormMetadata));
	}
}
