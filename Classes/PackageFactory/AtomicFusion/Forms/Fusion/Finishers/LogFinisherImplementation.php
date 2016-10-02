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
use TYPO3\Flow\Log\SystemLoggerInterface;

class LogFinisherImplementation extends AbstractFinisherImplementation
{
	/**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $logger;

	public function execute()
	{
		$message = $this->tsValue('message');
		$severity = 'LOG_' . strtoupper($this->tsValue('severity'));

		if (defined($severity)) {
			$severity = constant($severity);
		} else {
			$severity = LOG_INFO;
		}

		$this->logger->log($message, $severity);
	}
}
