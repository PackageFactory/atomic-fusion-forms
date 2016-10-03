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
use TYPO3\Eel\ProtectedContextAwareInterface;

class FieldContext implements ProtectedContextAwareInterface
{
	/**
	 * @var FormContext
	 */
	protected $formContext;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var array
	 */
	protected $propertyPath = [];

	/**
	 * @var string
	 */
	protected $argumentPropertyPath;

	public function __construct(FormContext $formContext, $name, $label, array $propertyPath = [])
	{
		$this->formContext = $formContext;
		$this->name = $name;
		$this->label = $label;
		$this->propertyPath = $propertyPath;
		$this->argumentPropertyPath = implode('.', [$this->name] + $this->propertyPath);
		$this->identifier = $formContext->getIdentifier() . '__' . str_replace('.', '_', $this->argumentPropertyPath);
	}

	public function getIdentifier()
	{
		return $this->identifier;
	}

	public function getName()
	{
		$result = $this->formContext->getArgumentNamespace();
		$result.= sprintf('[%s]', $this->name);

		if (count($this->propertyPath) > 0) {
			$result.= sprintf('[%s]', implode('][', $this->propertyPath));
		}

		return $result;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function getValue()
	{
		return $this->formContext->getFieldValueForPath($this->argumentPropertyPath);
	}

	public function getHasErrors()
	{
		return $this->formContext->errorsExistForPath($this->argumentPropertyPath);
	}

	public function getValidationResult()
	{
		return $this->formContext->getValidationResultForPath($this->argumentPropertyPath);
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
