<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Context;

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
use TYPO3\Eel\ProtectedContextAwareInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;

/**
 * Field context for use in fusion
 */
class FieldContext implements ProtectedContextAwareInterface
{
	/**
	 * @var FormRuntimeInterface
	 */
	protected $formRuntime;

	/**
	 * @var string
	 */
	protected $fieldName;

	/**
	 * @var string
	 */
	protected $propertyPath;

	/**
	 * Constructor
	 *
	 * @param FormRuntimeInterface $formRuntime
	 * @param string $fieldName
	 */
	public function __construct(FormRuntimeInterface $formRuntime, $fieldName, $propertyPath)
	{
		$this->formRuntime = $formRuntime;
		$this->fieldName = $fieldName;
		$this->propertyPath = $propertyPath;
	}

	/**
	 * Get the field label
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->formRuntime->getFormDefinition()->getFieldDefinition($this->fieldName)->getLabel();
	}

	/**
	 * Get the field name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->formRuntime->getFormDefinition()->getFieldDefinition($this->fieldName)->getName();
	}

    /**
     * Get the argument
     *
     * @return mixed
     */
    public function getArgument()
    {
        return $this->formRuntime->getArgument($this->propertyPath);
    }

    /**
     * Get the value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->formRuntime->getArgument($this->propertyPath);
    }

    /**
     * Get the validation result
     *
     * @return Result
     */
    public function getValidationResult()
    {
        return $this->formRuntime->getValidationResult()->forProperty($this->propertyPath);
    }

    /**
     * Check, if there are validation errors present for this field
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->getValidationResult()->hasErros();
    }

	/**
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
		return true;
    }
}
