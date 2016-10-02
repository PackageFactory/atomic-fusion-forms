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

class FormState
{
	/**
	 * @var boolean
	 */
	protected $__isInitialCall = true;

	/**
	 * Determine whether the form is initially called
	 *
	 * @return boolean
	 */
	public function isInitialCall()
	{
		return $this->__isInitialCall;
	}

	/**
	 * Indicate, that the form has already been called when this object gets
	 * unserialized
	 *
	 * @return void
	 */
	public function __wakeup()
	{
		$this->__isInitialCall = false;
	}
}
