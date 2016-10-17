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
use PackageFactory\AtomicFusion\Forms\Domain\Exception\FinisherRuntimeException;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FinisherRuntimeInterface;

/**
 * Finisher that adds a message to the response
 */
class MessageFinisher implements FinisherInterface
{
    /**
     * @var string
     */
    protected $message;

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
     * @inheritdoc
     */
    public function execute(FinisherRuntimeInterface $finisherRuntime)
    {
        if (!$this->message || (!is_string($this->message) && !method_exists($this->message, '__toString'))) {
            throw new FinisherRuntimeException(
                'Error in MessageFinisher: $message must be a string',
                1476546610
            );
        }

        $finisherRuntime->getResponse()->appendContent($this->message);
    }
}
