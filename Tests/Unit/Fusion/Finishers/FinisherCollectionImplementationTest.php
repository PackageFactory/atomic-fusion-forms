<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Finishers;

use Neos\Flow\Tests\UnitTestCase;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Finishers\FinisherCollectionImplementation;

class FinisherCollectionImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsListsOfFinisherDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherListImplementation = new FinisherCollectionImplementation($fusionRuntime, '', '');

        $finisherDefinition1 = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition2 = $this->createMock(FinisherDefinitionInterface::class);

        $fusionRuntime->method('getCurrentContext')->willReturn([]);

        $fusionRuntime->expects($this->exactly(4))
            ->method('evaluate')
            ->withConsecutive(
                ['/collection', $finisherListImplementation],
                ['/itemName', $finisherListImplementation],
                ['/finisherRenderer', $finisherListImplementation],
                ['/finisherRenderer', $finisherListImplementation]
            )
            ->will($this->onConsecutiveCalls(
                ['Item1', 'Item2'],
                'TheItemName',
                $finisherDefinition1,
                $finisherDefinition2
            ));

        $fusionRuntime->expects($this->exactly(2))
            ->method('pushContextArray')
            ->withConsecutive(
                [['TheItemName' => 'Item1']],
                [['TheItemName' => 'Item2']]
            );

        $fusionRuntime->expects($this->exactly(2))->method('popContext');

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
