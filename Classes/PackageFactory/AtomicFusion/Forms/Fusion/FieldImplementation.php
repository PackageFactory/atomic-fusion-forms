<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion;

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
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;

class FieldImplementation extends AbstractTypoScriptObject
{
	public function evaluate()
	{
		return [
			'name' => $this->tsValue('name'),
			'page' => $this->tsValue('page'),
			'label' => $this->tsValue('label'),
			'type' => $this->tsValue('type'),
			'validators' => $this->tsValue('validators')
		];
	}
}
