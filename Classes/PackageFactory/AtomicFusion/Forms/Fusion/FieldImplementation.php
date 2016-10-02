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
use PackageFactory\AtomicFusion\Forms\Eel\PropertyContextHelper;

class FieldImplementation extends AbstractTypoScriptObject
{
	public function evaluate()
	{
		$identifier = $this->tsValue('identifier');

		//
		// Render
		//
		$this->tsRuntime->pushContextArray([
			$this->tsValue('propertyContext') => new PropertyContextHelper($identifier),
			$this->tsValue('fieldContext') => [
				'identifier' => $identifier,
				'validators' => $this->tsValue('validators')
			]
		]);
		$renderedField = $this->tsRuntime->render(sprintf('%s/renderer', $this->path));
		$this->tsRuntime->popContext();

		return $renderedField;
	}
}
