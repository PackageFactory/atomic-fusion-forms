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
use Neos\Flow\Error\Result;
use Neos\Flow\Http\Response;

/**
 * Method definitions for finisher runtime
 */
interface FinisherStateInterface
{
    /**
     * Get the response
     *
     * @return Response
     */
    public function getResponse();

    /**
     * Get the result
     *
     * @return Result
     */
    public function getResult();

    /**
     * Get the flash message container
     *
     * @return FlashMessageContainer
     */
    public function getFlashMessageContainer();
}
