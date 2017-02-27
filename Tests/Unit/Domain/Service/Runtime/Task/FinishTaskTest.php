<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime\Task;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Flow\Http\Response;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finisher\FinisherInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\FinisherResolverInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FinisherStateInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\Factory\FinisherStateFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task\FinishTask;

class FinishTaskTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsFinisherStateFromGivenParentRequest()
    {
        $response = $this->createMock(Response::class);
        $finisherState = $this->createMock(FinisherStateInterface::class);

        $finisherStateFactory = $this->createMock(FinisherStateFactory::class);
        $finisherStateFactory->expects($this->once())
            ->method('createFinisherState')
            ->with($response)
            ->willReturn($finisherState);

        $finishTask = new FinishTask();
        $this->inject($finishTask, 'finisherStateFactory', $finisherStateFactory);

        $this->assertSame($finisherState, $finishTask->run([], $response));
    }

    /**
     * @test
     */
    public function resolvesFinishersAccordingToGivenFinisherDefinitions()
    {
        $response = $this->createMock(Response::class);
        $finisherState = $this->createMock(FinisherStateInterface::class);

        $finisherStateFactory = $this->createMock(FinisherStateFactory::class);
        $finisherStateFactory->method('createFinisherState')->willReturn($finisherState);

        $finisherDefinition1 = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition2 = $this->createMock(FinisherDefinitionInterface::class);

        $finisher = $this->createMock(FinisherInterface::class);

        //
        // Expect that the finisher resolver will be called twice with the passed finisher
        // definitions
        //
        $finisherResolver = $this->createMock(FinisherResolverInterface::class);
        $finisherResolver->expects($this->exactly(2))
            ->method('resolve')
            ->withConsecutive(
                [$this->identicalTo($finisherDefinition1)],
                [$this->identicalTo($finisherDefinition2)]
            )
            ->willReturn($finisher);

        $finishTask = new FinishTask();
        $this->inject($finishTask, 'finisherStateFactory', $finisherStateFactory);
        $this->inject($finishTask, 'finisherResolver', $finisherResolver);

        $finishTask->run([$finisherDefinition1, $finisherDefinition2], $response);
    }

    /**
     * @test
     */
    public function executesFinishersWithCreatedFinisherState()
    {
        $response = $this->createMock(Response::class);
        $finisherState = $this->createMock(FinisherStateInterface::class);

        $finisherStateFactory = $this->createMock(FinisherStateFactory::class);
        $finisherStateFactory->method('createFinisherState')->willReturn($finisherState);

        $finisherDefinition = $this->createMock(FinisherDefinitionInterface::class);

        $finisher1 = $this->createMock(FinisherInterface::class);
        $finisher2 = $this->createMock(FinisherInterface::class);

        $finisherResolver = $this->createMock(FinisherResolverInterface::class);
        $finisherResolver->method('resolve')->will(
            $this->onConsecutiveCalls($finisher1, $finisher2)
        );

        //
        // Expect that the finishers will be called with the created finisher runtime
        //
        $finisher1->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($finisherState));
        $finisher2->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($finisherState));

        $finishTask = new FinishTask();
        $this->inject($finishTask, 'finisherStateFactory', $finisherStateFactory);
        $this->inject($finishTask, 'finisherResolver', $finisherResolver);

        $finishTask->run([$finisherDefinition, $finisherDefinition], $response);
    }
}
