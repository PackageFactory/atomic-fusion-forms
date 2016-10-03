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
use TYPO3\TypoScript\TypoScriptObjects\AbstractArrayTypoScriptObject;

class FieldsImplementation extends AbstractArrayTypoScriptObject
{
	public function evaluate()
	{
		$result = [];
		foreach ($this->properties as $key => $value) {
			if ($this->tsRuntime->canRender(sprintf('%s/%s', $this->path, $key))) {
				$fieldConfiguration = $this->tsRuntime->render(sprintf('%s/%s', $this->path, $key));
			} else {
				$fieldConfiguration = $this->tsRuntime->render(sprintf('%s/%s<PackageFactory.AtomicFusion.Forms:Field>', $this->path, $key));
			}

			if (empty($fieldConfiguration['name'])) {
				$fieldConfiguration['name'] = $key;
			}

			$result[$fieldConfiguration['name']] = $fieldConfiguration;
		}

		return $result;
	}
}
