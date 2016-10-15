<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Model\Finishers;

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
use PackageFactory\AtomicFusion\Forms\Domain\Exception\FinisherRuntimeException;
use PackageFactory\AtomicFusion\Forms\Domain\Service\FinisherRuntime;

/**
 * Finisher that leaves a log message in the system log with configurable severity
 */
class LogFinisher implements FinisherInterface
{
    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $severity = LOG_INFO;

    /**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $logger;

    /**
     * Set the message
     *
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * Set the severity
     *
     * @param string $severity
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
    }

    /**
     * @inheritdoc
     */
    public function execute(FinisherRuntime $finisherRuntime)
    {
        $severity = 'LOG_' . strtoupper($this->severity);

        if (!defined($severity)) {
            throw new FinisherRuntimeException(
                sprintf('Error in LogFinisher: Severity %s is unknown', $severity),
                1476546610
            );
        }

        $this->logger->log($this->message, $severity);
    }
}
