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
 * Defines methods for finishers
 */
interface FinisherInterface
{
    /**
     * Execute this finisher
     *
     * @param FinisherStateInterface $finisherState
     * @return void
     * @throws FinisherStateException when the execution fails
     */
    public function execute(FinisherStateInterface $finisherState);
}
