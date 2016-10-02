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

class ValidatorImplementation extends AbstractTypoScriptObject
{
	protected function getClassName()
	{
		if ($className = $this->tsValue('className')) {
			return $className;
		}

		// TODO: Exception: Could not determine class name
	}
	public function evaluate()
	{
		return [
			'className' => $this->getClassName(),
			'options' => $this->tsValue('options'),
			'message' => sprintf('%s/message', $this->path)
		];
	}
}
