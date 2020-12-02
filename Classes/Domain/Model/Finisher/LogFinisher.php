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

use Neos\Flow\Annotations as Flow;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\FinisherStateException;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FinisherStateInterface;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
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
    public function execute(FinisherStateInterface $finisherState)
    {
        $severity = 'LOG_' . strtoupper($this->severity);

        if (!defined($severity)) {
            throw new FinisherStateException(
                sprintf('Error in LogFinisher: Severity %s is unknown', $severity),
                1476546610
            );
        }

        if (!$this->message || (!is_string($this->message) && !method_exists($this->message, '__toString'))) {
            throw new FinisherStateException(
                'Error in LogFinisher: $message must be a string',
                1476563413
            );
        }

        $this->logger->log(constant($severity), $this->message);
    }
}
