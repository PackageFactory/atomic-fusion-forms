<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Model\Finisher;

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
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FinisherRuntimeInterface;

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
    protected $severity = 'INFO';

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
    public function execute(FinisherRuntimeInterface $finisherRuntime)
    {
        $severity = 'LOG_' . strtoupper($this->severity);

        if (!defined($severity)) {
            throw new FinisherRuntimeException(
                sprintf('Error in LogFinisher: Severity %s is unknown', $severity),
                1476546610
            );
        }

        if (!$this->message || (!is_string($this->message) && !method_exists($this->message, '__toString'))) {
            throw new FinisherRuntimeException(
                'Error in LogFinisher: $message must be a string',
                1476563413
            );
        }

        $this->logger->log($this->message, constant($severity));
    }
}
