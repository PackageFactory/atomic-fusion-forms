<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Fields;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Fusion\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Fields\FieldCollectionImplementation;

class FieldCollectionImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsListsOfFieldDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $fieldListImplementation = new FieldCollectionImplementation($fusionRuntime, '', '');

        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);

        $fusionRuntime->method('getCurrentContext')->willReturn([]);

        $fusionRuntime->expects($this->exactly(4))
            ->method('evaluate')
            ->withConsecutive(
                ['/collection', $fieldListImplementation],
                ['/itemName', $fieldListImplementation],
                ['/fieldRenderer', $fieldListImplementation],
                ['/fieldRenderer', $fieldListImplementation]
            )
            ->will($this->onConsecutiveCalls(
                ['Item1', 'Item2'],
                'TheItemName',
                $fieldDefinition1,
                $fieldDefinition2
            ));

        $fusionRuntime->expects($this->exactly(2))
            ->method('pushContextArray')
            ->withConsecutive(
                [['TheItemName' => 'Item1']],
                [['TheItemName' => 'Item2']]
            );

        $fusionRuntime->expects($this->exactly(2))->method('popContext');

        $fieldDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn('fieldName1');

        $fieldDefinition2->expects($this->once())
            ->method('getName')
            ->willReturn('fieldName2');

        $fieldDefinitionMap = $fieldListImplementation->evaluate();

        $this->assertSame($fieldDefinition1, $fieldDefinitionMap['fieldName1']);
        $this->assertSame($fieldDefinition2, $fieldDefinitionMap['fieldName2']);
    }
}
