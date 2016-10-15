<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service;

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
use TYPO3\Flow\Mvc\FlashMessageContainer;
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Http\Response;

class FinisherRuntime
{
    /**
     * @Flow\Inject
     * @var Result
     */
    protected $result;

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
    }

    /**
     * Get the result
     *
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Get the response
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the flash message container
     *
     * @return FlashMessageContainer
     */
    public function getFlashMessageContainer()
    {
        return $this->flashMessageContainer;
    }
}
