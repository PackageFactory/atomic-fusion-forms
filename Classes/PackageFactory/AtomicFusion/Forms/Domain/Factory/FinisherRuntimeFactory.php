<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Factory;

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
use PackageFactory\AtomicFusion\Forms\Domain\Service\FinisherRuntime;
use TYPO3\Flow\Http\Response;

/**
 * Create finisher runtimes
 */
class FinisherRuntimeFactory
{
    /**
     * Create a new finisher runtime
     *
     * @param Response $parentResponse
     * @return FinisherRuntime
     */
    public function createFinisherRuntime(Response $parentResponse)
    {
        $response = new Response($parentResponse);

        return new FinisherRuntime($response);
    }
}
