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
use TYPO3\Flow\Property\PropertyMappingConfiguration;

interface ProcessorInterface
{
	public function preProcess(PropertyMappingConfiguration $propertyMappingConfiguration);

	public function postProcess(Result $result);
}
