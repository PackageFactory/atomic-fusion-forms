<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Model;

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
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Error\Error;
use TYPO3\Flow\Validation\ValidatorResolver;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use TYPO3\Flow\Validation\Validator\ValidatorInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Fusion\FusionAwareInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Fusion\FusionAwareTrait;

class LateResolvingValidatorDecorator implements ValidatorInterface, FusionAwareInterface
{
	use FusionAwareTrait;

	/**
     * @Flow\Inject
     * @var ValidatorResolver
     */
    protected $validatorResolver;

	/**
	 * @var string
	 */
	protected $validatorClassName;

	/**
	 * @var string
	 */
	protected $fusionPathToOptions;

	/**
	 * @var string
	 */
	protected $fusionPathToCustomMessage;

	/**
	 * @var array
	 */
	protected $renderedOptions = null;

	/**
	 * @var string
	 */
	protected $renderedCustomMessage = null;

	/**
     * Constructs the validator and sets validation options
     *
     * @param array $options The validation options
     */
	public function __construct(array $options = [])
	{
		//
		// Empty constructor, we don't do anything with those options. This is
		// merely a decorator for later to be resolved validators
		//
	}

	/**
	 * Set the validator class name
	 *
	 * @param string $validatorClassName
	 * @return void
	 */
	public function setValidatorClassName($validatorClassName)
	{
		$this->validatorClassName = $validatorClassName;
	}

	/**
	 * Set the fusion path for later rendering of the options array
	 *
	 * @param string $fusionPathToOptions
	 * @return void
	 */
	public function setFusionPathToOptions($fusionPathToOptions)
	{
		$this->fusionPathToOptions = $fusionPathToOptions;
	}

	/**
	 * Set the fusion path for later rendering of the custom message
	 *
	 * @param string $fusionPathToCustomMessage
	 * @return void
	 */
	public function setFusionPathToCustomMessage($fusionPathToCustomMessage)
	{
		$this->fusionPathToCustomMessage = $fusionPathToCustomMessage;
	}

    /**
     * Resolve the configured validator and run it
     *
     * @param mixed $value The value that should be validated
     * @return ErrorResult
     */
    public function validate($value)
	{
		$options = $this->getOptions();
		$validator = $this->validatorResolver->createValidator($this->validatorClassName, $options);
		$result = new Result();

		$validationResult = $validator->validate($value);

		if ($customMessage = $this->getCustomMessage()) {
			$result->addError(new Error($customMessage));
		} else {
			$result->merge($validationResult);
		}

		return $result;
	}

    /**
     * Render the options
     *
     * @return array
     */
    public function getOptions()
	{
		if ($this->renderedOptions === null) {
			if ($this->fusionRuntime->canRender($this->fusionPathToOptions)) {
				$this->renderedOptions = $this->fusionRuntime->render($this->fusionPathToOptions);
			}

			if ($this->renderedOptions === null) {
				$this->renderedOptions = [];
			}
		}

		return $this->renderedOptions;
	}

	/**
     * Render the custom message
     *
     * @return string|null
     */
	public function getCustomMessage()
	{
		if ($this->renderedCustomMessage === null) {
			if ($this->fusionRuntime->canRender($this->fusionPathToCustomMessage)) {
				$this->renderedCustomMessage = $this->fusionRuntime->render($this->fusionPathToCustomMessage);
			}

			if ($this->renderedCustomMessage === null) {
				$this->renderedCustomMessage = '';
			}
		}

		if ($this->renderedCustomMessage === '') {
			return null;
		}

		return $this->renderedCustomMessage;
	}
}
