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
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FinisherStateInterface;

/**
 * Finisher that does nothing for testing purposes
 *
 * @codeCoverageIgnore
 */
class NullFinisher implements FinisherInterface
{
    /**
     * @inheritdoc
     */
    public function execute(FinisherStateInterface $finisherState)
    {
        //
        // Nothing happens
        //
    }
}
