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
use Neos\Flow\Http\Response;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\Factory\FinisherStateFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\FinisherResolverInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;

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
    public function shouldRun(FormRuntimeInterface $runtime)
    {
        $pageDefinitions = $runtime->getFormDefinition()->getPageDefinitions();
        $isOnLastPage = false;

        if (is_array($pageDefinitions)) {
            $lastPageDefinition = array_pop($pageDefinitions);
            if ($lastPageDefinition) {
                $isOnLastPage = $runtime->getFormState()->getCurrentPage() === $lastPageDefinition->getName();
            }
        }

        return !$runtime->getFormState()->isInitialCall() && !$runtime->getFormState()->getValidationResult()->hasErrors() && (
                !$runtime->getFormDefinition()->hasPages() || $isOnLastPage
            );
    }

    /**
     * @inheritdoc
     */
    public function run(FormRuntimeInterface $runtime, Response $parentResponse)
    {
        $finisherDefinitions = $runtime->getFormDefinition()->getFinisherDefinitions();

        return $this->finish($finisherDefinitions, $parentResponse);

    }

    /**
     * Run all defined finishers
     *
     * @param array<FinisherDefinitionInterface> $finisherDefinitions
     * @param Response $parentResponse
     * @return void
     */
    protected function finish(array $finisherDefinitions, Response $parentResponse)
    {
        $finisherState = $this->finisherStateFactory->createFinisherState($parentResponse);

        foreach ($finisherDefinitions as $finisherDefinition) {
            $finisher = $this->finisherResolver->resolve($finisherDefinition);
            $finisher->execute($finisherState);
        }

        return $finisherState;
    }
}
