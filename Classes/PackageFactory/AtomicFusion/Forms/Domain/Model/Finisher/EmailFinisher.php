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
use Neos\Utility\MediaTypes;

/**
 * Finisher that sends an email message
 */
class EmailFinisher implements FinisherInterface
{
    const FORMAT_PLAINTEXT = 'plaintext';
    const FORMAT_HTML = 'html';

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $recipientName;

    /**
     * @var string
     */
    protected $recipientAddress;

    /**
     * @var string
     */
    protected $senderName;

    /**
     * @var string
     */
    protected $senderAddress;

    /**
     * @var string
     */
    protected $replyToAddress;

    /**
     * @var string
     */
    protected $carbonCopyAddress;

    /**
     * @var string
     */
    protected $blindCarbonCopyAddress;

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $testMode;

    /**
     * @var array
     */
    protected $embeddedFiles;

    /**
     * @var array
     */
    protected $attachments;

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @param string $recipientName
     */
    public function setRecipientName($recipientName)
    {
        $this->recipientName = $recipientName;
    }

    /**
     * @param string $recipientAddress
     */
    public function setRecipientAddress($recipientAddress)
    {
        $this->recipientAddress = $recipientAddress;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * @param string $senderAddress
     */
    public function setSenderAddress($senderAddress)
    {
        $this->senderAddress = $senderAddress;
    }

    /**
     * @param string $replyToAddress
     */
    public function setReplyToAddress($replyToAddress)
    {
        $this->replyToAddress = $replyToAddress;
    }

    /**
     * @param string $carbonCopyAddress
     */
    public function setCarbonCopyAddress($carbonCopyAddress)
    {
        $this->carbonCopyAddress = $carbonCopyAddress;
    }

    /**
     * @param string $blindCarbonCopyAddress
     */
    public function setBlindCarbonCopyAddress($blindCarbonCopyAddress)
    {
        $this->blindCarbonCopyAddress = $blindCarbonCopyAddress;
    }

    /**
     * @param string $format
     */
    public function setFormat($format)
    {
        $this->format = $format;
    }

    /**
     * @param string $testMode
     */
    public function setTestMode($testMode)
    {
        $this->testMode = $testMode;
    }

    /**
     * @param array $embeddedFiles
     */
    public function setEmbeddedFiles($embeddedFiles)
    {
        $this->embeddedFiles = $embeddedFiles;
    }

    /**
     * @param array $attachments
     */
    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
    }

    /**
     * @inheritdoc
     */
    public function execute(FinisherStateInterface $finisherState)
    {
        if (!$this->subject || (!is_string($this->subject) && !method_exists($this->subject, '__toString'))) {
            throw new FinisherStateException(
                'Error in EmailFinisher: $subject must be a string',
                1488995461
            );
        }

        if (!$this->recipientAddress) {
            throw new FinisherStateException(
                'Error in EmailFinisher: $recipientAddress must be a set',
                1488995466
            );
        }

        if (is_array($this->recipientAddress) && $this->recipientName !== '') {
            throw new FinisherStateException(
                'Error in EmailFinisher:  "recipientName" cannot be used with multiple recipients in the EmailFinisher',
                1488995476
            );
        }


        if (!$this->senderAddress || (!is_string($this->senderAddress) && !method_exists($this->senderAddress, '__toString'))) {
            throw new FinisherStateException(
                'Error in EmailFinisher: $senderAddress must be a string',
                1488995483
            );
        }


        $mail = new \Neos\SwiftMailer\Message();

        $mail
            ->setFrom(array($this->senderAddress => $this->senderName))
            ->setSubject($this->subject);

        if (is_array($this->recipientAddress)) {
            $mail->setTo($this->recipientAddress);
        } else {
            $mail->setTo(array($this->recipientAddress => $this->recipientName));
        }

        if ($this->replyToAddress !== null) {
            $mail->setReplyTo($this->replyToAddress);
        }

        if ($this->carbonCopyAddress !== null) {
            $mail->setCc($this->carbonCopyAddress);
        }

        if ($this->blindCarbonCopyAddress !== null) {
            $mail->setBcc($this->blindCarbonCopyAddress);
        }

        if ($this->format === self::FORMAT_PLAINTEXT) {
            $mail->setBody($this->message, 'text/plain');
        } else {
            $mail->setBody($this->message, 'text/html');
        }

        if ($this->embeddedFiles && is_array($this->embeddedFiles)) {
            foreach ($this->embeddedFiles as $identifier => $fileReference) {
                $fileName = basename($fileReference);
                $mediaType = MediaTypes::getMediaTypeFromFilename($fileName);
                $embeddable = new \Swift_EmbeddedFile(file_get_contents($fileReference), $fileName, $mediaType);
                $embeddable->setId($identifier);
                $mail->embed($embeddable);
            }
        }

        if ($this->attachments && is_array($this->attachments)) {
            foreach ($this->attachments as $identifier => $fileReference) {
                $fileName = basename($fileReference);
                $mediaType = MediaTypes::getMediaTypeFromFilename($fileName);
                $attachment = new \Swift_Attachment(file_get_contents($fileReference), $fileName, $mediaType);
                $attachment->setId($identifier);
                $mail->attach($attachment);
            }
        }

        if ($this->testMode === true) {
            $finisherState->getResponse()->appendContent(
                \Neos\Flow\var_dump(
                    array(
                        'sender' => array($this->senderAddress => $this->senderName),
                        'recipients' => is_array($this->recipientAddress) ? $this->recipientAddress : array($this->recipientAddress => $this->recipientName),
                        'replyToAddress' => $this->replyToAddress,
                        'carbonCopyAddress' => $this->carbonCopyAddress,
                        'blindCarbonCopyAddress' => $this->blindCarbonCopyAddress,
                        'message' => $this->message,
                        'format' => $this->format,
                    ),
                    'E-Mail "' . $this->subject . '"',
                    true
                )
            );
        } else {
            $mail->send();
        }
    }
}
