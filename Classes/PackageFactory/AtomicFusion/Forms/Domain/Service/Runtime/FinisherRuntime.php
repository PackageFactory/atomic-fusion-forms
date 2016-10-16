<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime;

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

class FinisherRuntime implements FinisherRuntimeInterface
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @Flow\Inject
     * @var Result
     */
    protected $result;

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
