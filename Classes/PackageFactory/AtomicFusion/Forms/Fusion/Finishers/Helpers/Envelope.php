<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Finishers\Helpers;

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
use PackageFactory\AtomicFusion\Forms\Fusion\Finishers\AbstractFinisherImplementation;

class Envelope
{
	/**
	 * @var AbstractFinisherImplementation
	 */
	protected $finisher;

	/**
	 * Constructor
	 *
	 * @param AbstractFinisherImplementation $finisher
	 */
	public function __construct(AbstractFinisherImplementation $finisher)
	{
		$this->finisher = $finisher;
	}

	/**
	 * Execute the finisher
	 *
	 * @return mixed
	 */
	public function execute()
	{
		return $this->finisher->execute();
	}
}
