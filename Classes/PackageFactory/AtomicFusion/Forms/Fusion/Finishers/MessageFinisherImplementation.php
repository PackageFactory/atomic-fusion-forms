<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Finishers;

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

class MessageFinisherImplementation extends AbstractFinisherImplementation
{
	public function execute()
	{
		return $this->tsValue('renderer');
	}
}