<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Finishers;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Fusion\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Finishers\FinisherListImplementation;

class FinisherListImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsListsOfFinisherDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherListImplementation = new FinisherListImplementation($fusionRuntime, '', '');

        $finisherDefinition1 = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition2 = $this->createMock(FinisherDefinitionInterface::class);

        $this->inject($finisherListImplementation, 'properties', [
            'finisher1' => [
                '__objectType' => 'SomeFusionObject'
            ],
            'finisher2' => [
                '__objectType' => 'SomeOtherFusionObject'
            ]
        ]);

        $fusionRuntime->expects($this->exactly(2))
            ->method('render')
            ->withConsecutive(['/finisher1'], ['/finisher2'])
            ->will($this->onConsecutiveCalls(
                $finisherDefinition1,
                $finisherDefinition2
            ));

        $finisherDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn('finisherName1');

        $finisherDefinition2->expects($this->once())
            ->method('getName')
            ->willReturn('finisherName2');

        $finisherDefinitionMap = $finisherListImplementation->evaluate();

        $this->assertSame($finisherDefinition1, $finisherDefinitionMap['finisherName1']);
        $this->assertSame($finisherDefinition2, $finisherDefinitionMap['finisherName2']);
    }
}
