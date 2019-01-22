<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\State\Factory;

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
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FinisherState;
use Neos\Flow\Http\Response;

/**
 * Create finisher states
 *
 * @Flow\Scope("singleton")
 */
class FinisherStateFactory
{
    /**
     * Create a new finisher state
     *
     * @param Response $parentResponse
     * @return FinisherState
     */
    public function createFinisherState(Response $parentResponse)
    {
        $response = new Response($parentResponse);

        return new FinisherState($response);
    }
}
