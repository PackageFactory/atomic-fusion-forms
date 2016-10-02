<?php
namespace PackageFactory\AtomicFusion\Forms\Eel;

/**
 * This file is part of the PackageFactory.AtomicFusion.Forms package
 *
 * (c) 2016 Wilhelm Behncke <wilhelm.behncke@googlemail.com>
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Eel\ProtectedContextAwareInterface;

class PropertyContextHelper implements ProtectedContextAwareInterface
{
	/**
	 * @var string
	 */
	protected $fieldNamePrefix;

	public function __construct($fieldNamePrefix)
	{
		$this->fieldNamePrefix = $fieldNamePrefix;
	}

	/**
	 * Generate a proper field name from a given context path
	 *
	 * @param string $propertyPath
	 * @return string
	 */
	public function name($propertyPath)
	{
		$parts = explode('.', $propertyPath);
		return $this->fieldNamePrefix . '[' . implode('][', $parts) . ']';
	}

	/**
	 * All methods are considered safe
	 *
	 * @param string $methodName
	 * @return boolean
	 */
	public function allowsCallOfMethod($methodName)
	{
		return true;
	}
}
