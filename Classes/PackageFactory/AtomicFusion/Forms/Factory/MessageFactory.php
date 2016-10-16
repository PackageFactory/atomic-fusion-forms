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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Property\PropertyMapper;
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Error\Error;

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
    public function createError()
    {
        return new Error();
    }
}
