<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime\Tasks;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Http\Response;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finishers\FinisherInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Factory\FinisherRuntimeFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\FinisherResolverInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FinisherRuntimeInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Tasks\FinishTask;

class FinishTaskTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsFinisherRuntimeFromGivenParentRequest()
    {
        $response = $this->createMock(Response::class);
        $finisherRuntime = $this->createMock(FinisherRuntimeInterface::class);

        $finisherRuntimeFactory = $this->createMock(FinisherRuntimeFactory::class);
        $finisherRuntimeFactory->expects($this->once())
            ->method('createFinisherRuntime')
            ->with($response)
            ->willReturn($finisherRuntime);

        $finishTask = new FinishTask();
        $this->inject($finishTask, 'finisherRuntimeFactory', $finisherRuntimeFactory);

        $this->assertSame($finisherRuntime, $finishTask->run([], $response));
    }

    /**
     * @test
     */
    public function resolvesFinishersAccordingToGivenFinisherDefinitions()
    {
        $response = $this->createMock(Response::class);
        $finisherRuntime = $this->createMock(FinisherRuntimeInterface::class);

        $finisherRuntimeFactory = $this->createMock(FinisherRuntimeFactory::class);
        $finisherRuntimeFactory->method('createFinisherRuntime')->willReturn($finisherRuntime);

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
        $this->inject($finishTask, 'finisherRuntimeFactory', $finisherRuntimeFactory);
        $this->inject($finishTask, 'finisherResolver', $finisherResolver);

        $finishTask->run([$finisherDefinition1, $finisherDefinition2], $response);
    }

    /**
     * @test
     */
    public function executesFinishersWithCreaatedFinisherRuntime()
    {
        $response = $this->createMock(Response::class);
        $finisherRuntime = $this->createMock(FinisherRuntimeInterface::class);

        $finisherRuntimeFactory = $this->createMock(FinisherRuntimeFactory::class);
        $finisherRuntimeFactory->method('createFinisherRuntime')->willReturn($finisherRuntime);

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
            ->with($this->identicalTo($finisherRuntime));
        $finisher2->expects($this->once())
            ->method('execute')
            ->with($this->identicalTo($finisherRuntime));

        $finishTask = new FinishTask();
        $this->inject($finishTask, 'finisherRuntimeFactory', $finisherRuntimeFactory);
        $this->inject($finishTask, 'finisherResolver', $finisherResolver);

        $finishTask->run([$finisherDefinition, $finisherDefinition], $response);
    }
}
