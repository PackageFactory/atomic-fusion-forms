<?php
namespace PackageFactory\AtomicFusion\Forms\Factory;

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
use Neos\Flow\Property\PropertyMapper;
use Neos\Error\Messages\Result;
use Neos\Error\Messages\Error;

/**
 * @Flow\Scope("singleton")
 */
class MessageFactory
{
    /**
     * Create a new result
     *
     * @return Result
     */
    public function createResult()
    {
        return new Result();
    }

    /**
     * Create a new error
     *
     * @return Error
     */
    public function createError($message = '')
    {
        return new Error($message);
    }
}
