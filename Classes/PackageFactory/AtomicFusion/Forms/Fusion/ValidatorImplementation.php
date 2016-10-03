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
use PackageFactory\AtomicFusion\Forms\Domain\Model\Validator;

class ValidatorImplementation extends AbstractTypoScriptObject
{
	protected function getClassName()
	{
		if ($className = $this->tsValue('className')) {
			return $className;
		}

		throw new \Exception('A Validator must providae a class name', 1475486564);
	}
	
	public function evaluate()
	{
		$validator = new Validator();
		$validator->setValidatorClassName($this->getClassName());
		$validator->setFusionPathToOptions(sprintf('%s/options', $this->path));
		$validator->setFusionPathToCustomMessage(sprintf('%s/message', $this->path));
		$validator->setFusionRuntime($this->tsRuntime);

		return $validator;
	}
}
