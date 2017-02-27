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

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Error\Result;
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
	 * @param string $propertyPath
	 */
	public function __construct(FormRuntimeInterface $formRuntime, $fieldName, $propertyPath = '')
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
		$name = $this->formRuntime->getFormDefinition()->getFieldDefinition($this->fieldName)->getName();
		$name = sprintf('[%s]', $name);

		if ($this->propertyPath !== '') {
			$propertyPathParts = explode('.', $this->propertyPath);
			$name .= '[' . implode('][', $propertyPathParts) . ']';
		}

		return $this->formRuntime->getRequest()->getArgumentNamespace() . $name;
	}

    /**
     * Get the argument
     *
     * @return mixed
     */
    public function getArgument()
    {
		$finalPropertyPath = $this->fieldName;
		if ($this->propertyPath !== '') {
			$finalPropertyPath .= '.' . $this->propertyPath;
		}

        return $this->formRuntime->getFormState()->getArgument($finalPropertyPath);
    }

    /**
     * Get the value
     *
     * @return mixed
     */
    public function getValue()
    {
		$finalPropertyPath = $this->fieldName;
		if ($this->propertyPath !== '') {
			$finalPropertyPath .= '.' . $this->propertyPath;
		}

        return $this->formRuntime->getFormState()->getValue($finalPropertyPath);
    }

    /**
     * Get the validation result
     *
     * @return Result
     */
    public function getValidationResult()
    {
		$finalPropertyPath = $this->fieldName;
		if ($this->propertyPath !== '') {
			$finalPropertyPath .= '.' . $this->propertyPath;
		}

        return $this->formRuntime->getFormState()->getValidationResult()->forProperty($finalPropertyPath);
    }

    /**
     * Check, if there are validation errors present for this field
     *
     * @return boolean
     */
    public function getHasErrors()
    {
        return $this->getValidationResult()->hasErrors();
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
