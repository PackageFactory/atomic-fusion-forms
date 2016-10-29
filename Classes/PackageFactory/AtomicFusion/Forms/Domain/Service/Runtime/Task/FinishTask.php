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
use TYPO3\Flow\Http\Response;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\Factory\FinisherStateFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\FinisherResolverInterface;

/**
 * Run finishers
 *
 * @Flow\Scope("singleton")
 */
class FinishTask implements FinishTaskInterface
{
    /**
     * @Flow\Inject
     * @var FinisherResolverInterface
     */
    protected $finisherResolver;

    /**
     * @Flow\Inject
     * @var FinisherStateFactory
     */
    protected $finisherStateFactory;

    /**
     * @inheritdoc
     */
    public function run(array $finisherDefinitions, Response $parentResponse)
    {
        $finisherState = $this->finisherStateFactory->createFinisherState($parentResponse);

        foreach ($finisherDefinitions as $finisherDefinition) {
            $finisher = $this->finisherResolver->resolve($finisherDefinition);
            $finisher->execute($finisherState);
        }

        return $finisherState;
    }
}
