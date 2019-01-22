<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\State;

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
use Neos\Flow\Mvc\FlashMessageContainer;
use Neos\Error\Messages\Result;
use Neos\Flow\Http\Response;

class FinisherState implements FinisherStateInterface
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @Flow\Inject
     * @var FlashMessageContainer
     */
    protected $flashMessageContainer;

    /**
     * Constructor
     *
     * @param FormRuntime $formRuntime
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->result = new Result();
    }

    /**
     * @inheritdoc
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @inheritdoc
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @inheritdoc
     */
    public function getFlashMessageContainer()
    {
        return $this->flashMessageContainer;
    }
}
