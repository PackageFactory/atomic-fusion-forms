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

use Neos\Flow\Annotations as Flow;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;
use Neos\Flow\Mvc\ActionResponse as Response;

/**
 * Method definitions for generic the finish task
 */
interface FinishTaskInterface
{

    /**
     * Should this task run
     *
     * @param array<FinisherDefinitionInterface> $finisherDefinitions
     * @return boolean
     */
    public function shouldRun(FormRuntimeInterface $runtime);

    /**
     * Run the defined task
     *
     * @param FormRuntimeInterface $runtime
     * @param Respone $parentResponse
     * @return void
     */
    public function run(FormRuntimeInterface $runtime, Response $parentResponse);
}
