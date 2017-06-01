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

/**
 * Finisher that sends an email message
 */
class RedirectFinisher implements FinisherInterface
{
    /**
     * @var string
     */
    protected $delay;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var string
     */
    protected $statusCode;

    /**
     * @param string $delay
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;
    }

    /**
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * @param string $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @inheritdoc
     */
    public function execute(FinisherStateInterface $finisherState)
    {
        if (!$this->uri || (!is_string($this->uri) && !method_exists($this->uri, '__toString'))) {
            throw new FinisherStateException(
                'Error in RedirectFinisher: $uri must be a string',
                1488995174
            );
        }

        $escapedUri = htmlentities($this->uri, ENT_QUOTES, 'utf-8');
        $response = $finisherState->getResponse();

        if ($this->delay == 0) {
            $mainResponse = $response->getParentResponse();
            $mainResponse->setHeader('Location', (string)$this->uri);
            $mainResponse->setStatus((int)$this->statusCode);
            $mainResponse->setContent(sprintf('<html><head><meta http-equiv="refresh" content="%s;url=%s"/></head></html>' . $this->delay . ';url=' . $escapedUri));
        } else {
            $response->appendContent(sprintf('<meta http-equiv="refresh" content="%s;url=%s"/>', $this->delay , $escapedUri));
        }
    }
}
