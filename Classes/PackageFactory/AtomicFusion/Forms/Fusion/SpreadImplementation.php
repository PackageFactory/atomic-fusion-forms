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
use TYPO3\Flow\Utility\Arrays;

class SpreadImplementation extends AbstractTypoScriptObject
{
	final public function evaluate()
	{
		return new Helpers\Spread($this);
	}

	public function checkPath($path)
	{
		return $this->tsRuntime->canRender(sprintf('%s/%s', $this->path, $path));
	}

	public function getValueAtPath($path, $additionalContext = [])
	{
		if (count($additionalContext) > 0 || strpos($path, '/') !== false) {
			$this->tsRuntime->pushContextArray(
				Arrays::arrayMergeRecursiveOverrule(
					$this->tsRuntime->getCurrentContext(),
					$additionalContext
				)
			);
			$result = $this->tsRuntime->render(sprintf('%s/%s', $this->path, $path));
			$this->tsRuntime->popContext();

			return $result;
		}

		return $this->tsValue($path);
	}
}
