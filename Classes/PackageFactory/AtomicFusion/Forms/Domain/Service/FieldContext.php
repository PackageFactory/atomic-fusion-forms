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

	public function __construct(FormContext $formContext, $name, array $propertyPath = [])
	{
		$this->formContext = $formContext;
		$this->name = $name;
		$this->propertyPath = $propertyPath;
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

	public function getValue()
	{
		$argumentPropertyPath = implode('.', [$this->name] + $this->propertyPath);

		return $this->formContext->getFieldValueForPath($argumentPropertyPath);
	}

	public function getHasErrors()
	{
		$argumentPropertyPath = implode('.', [$this->name] + $this->propertyPath);

		return $this->formContext->errorsExistForPath($argumentPropertyPath);
	}

	public function getValidationResult()
	{
		$argumentPropertyPath = implode('.', [$this->name] + $this->propertyPath);

		return $this->formContext->getValidationResultForPath($argumentPropertyPath);
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
