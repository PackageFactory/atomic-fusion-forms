<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task;

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
use TYPO3\Flow\Error\Result;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FinisherRuntimeInterface;
use TYPO3\Flow\Http\Response;

/**
 * Method definitions for the finish task
 */
interface FinishTaskInterface
{
    /**
     * Run all defined finishers
     *
     * @param array<FinisherDefinitionInterface> $finisherDefinitions
     * @param Response $parentResponse
     * @return void
     */
    public function run(array $finisherDefinitions, Response $parentResponse);
}
