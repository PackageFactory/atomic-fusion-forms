<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service;

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
use PackageFactory\AtomicFusion\Forms\Service\CryptographyService;

class FormContext
{
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $identifier;

	/**
	 * @var string
	 */
	protected $argumentNamespace;

	/**
	 * @var array
	 */
	protected $properties;

	/**
	 * @var ActionRequest
	 */
	protected $request;

	/**
	 * @var FormState
	 */
	protected $state;

	/**
	 * @Flow\Inject
	 * @var CryptographyService
	 */
	protected $cryptographyService;

	public function __construct($path, array $properties, ActionRequest $request)
	{
		$this->path = $path;
		$this->identifier = md5($this->path);
		$this->argumentNamespace = '--' . $this->identifier;
		$this->properties = $properties;

		//
		// Create sub request
		//
		$rootRequest = $request->getMainRequest() ?: $request;
        $pluginArguments = $rootRequest->getPluginArguments();

        $this->request = new ActionRequest($request);
        $this->request->setArgumentNamespace($this->argumentNamespace);


        if (isset($pluginArguments[$this->identifier])) {
            $this->request->setArguments($pluginArguments[$this->identifier]);
        }
	}

	/**
	 * Restore or initialize form state
	 * @return void
	 */
	protected function initializeObject()
	{
		if ($serializedFormStateWithHmac = $this->request->getInternalArgument('__state')) {
			$this->formState = $this->cryptographyService->decodeHiddenFormMetadata($serializedFormStateWithHmac);
		} else {
			$this->formState = new FormState();
		}
	}

	/**
	 * Get the identifier
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * Get the argument namespace
	 *
	 * @return string
	 */
	public function getArgumentNamespace()
	{
		return $this->argumentNamespace;
	}

	/**
	 * Get the form state
	 *
	 * @return FormState
	 */
	public function getFormState()
	{
		return $this->formState;
	}

	/**
	 * Get the encoded form state in preparation for rendering it within a hidden input tag
	 *
	 * @return string
	 */
	public function getEncodedFormState()
	{
		return $this->cryptographyService->encodeHiddenFormMetadata($this->formState);
	}
}
