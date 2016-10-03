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
use TYPO3\Eel\ProtectedContextAwareInterface;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Flow\Error\Result;
use PackageFactory\AtomicFusion\Forms\Service\CryptographyService;

class FormContext implements ProtectedContextAwareInterface
{
	/**
	 * @var string
	 */
	protected $path;

	/**
	 * @var string
	 */
	protected $action;

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
	protected $fields;

	/**
	 * @var array
	 */
	protected $finishers;

	/**
	 * @var ActionRequest
	 */
	protected $request;

	/**
	 * @var FormState
	 */
	protected $state;

	/**
	 * @var Result
	 */
	protected $validationResult;

	/**
	 * @var array
	 */
	protected $arguments;

	/**
	 * @Flow\Inject
	 * @var CryptographyService
	 */
	protected $cryptographyService;

	public function __construct($path, $action, array $fields, array $finishers, ActionRequest $request)
	{
		$this->path = $path;
		$this->action = $action;
		$this->identifier = md5($this->path);
		$this->argumentNamespace = '--' . $this->identifier;
		$this->fields = $fields;
		$this->finishers = $finishers;
		$this->validationResult = new Result();

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
		if ($serializedFormState = $this->request->getInternalArgument('__state')) {
			$this->formState = $this->cryptographyService->decodeHiddenFormMetadata($serializedFormState);
		} else {
			$this->formState = new FormState();
		}

		$this->arguments = Arrays::arrayMergeRecursiveOverrule(
			$this->formState->getArguments(),
			$this->request->getArguments()
		);
	}

	/**
	 * Get the identifier
	 *
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
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

	public function field($fieldName)
	{
		$propertyPath = explode('.', $fieldName);
		$name = array_shift($propertyPath);

		if (!isset($this->fields[$name])) {
			throw new \Exception(sprintf('Field `%s` is currently not configured.', $name), 1475433971);
		}

		return new FieldContext($this, $name, $this->fields[$name]['label'], $propertyPath);
	}

	public function getFieldValueForPath($path)
	{
		return Arrays::getValueByPath($this->arguments, $path);
	}

	public function getFieldConfiguration()
	{
		return $this->fields;
	}

	public function setValidationResult(Result $validationResult)
	{
		$this->validationResult = $validationResult;
	}

	public function errorsExistForPath($path)
	{
		return $this->validationResult->forProperty($path)->hasErrors();
	}

	public function getValidationResultForPath($path)
	{
		return $this->validationResult->forProperty($path);
	}


	public function persistRequestArguments()
	{
		$this->formState->setArguments($this->arguments);
	}

	/**
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
		switch ($methodName) {
			case 'getAction':
			case 'getIdentifier':
			case 'field':
				return true;

			default:
				return false;
		}
    }
}
