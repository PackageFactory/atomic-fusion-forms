<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Fields;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\FieldDefinitionMapFactoryInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\FieldDefinitionFactoryInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Fields\FieldCollectionImplementation;

class FieldCollectionImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function evaluatesToAFormDefinitionMapFactory()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $fieldListImplementation = new FieldCollectionImplementation($fusionRuntime, '', '');

        $this->assertTrue($fieldListImplementation->evaluate() instanceof FieldDefinitionMapFactoryInterface);
    }

    /**
     * @test
     */
    public function createsListsOfFieldDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldListImplementation = new FieldCollectionImplementation($fusionRuntime, '', '');

        $fieldDefinitionFactory1 = $this->createMock(FieldDefinitionFactoryInterface::class);
        $fieldDefinitionFactory2 = $this->createMock(FieldDefinitionFactoryInterface::class);
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
                $fieldDefinitionFactory1,
                $fieldDefinitionFactory2
            ));

        $fusionRuntime->expects($this->exactly(2))
            ->method('pushContextArray')
            ->withConsecutive(
                [['TheItemName' => 'Item1']],
                [['TheItemName' => 'Item2']]
            );

        $fusionRuntime->expects($this->exactly(2))->method('popContext');

        $fieldDefinitionFactory1->expects($this->once())
            ->method('createFieldDefinition')
            ->with($formDefinition)
            ->willReturn($fieldDefinition1);

        $fieldDefinitionFactory2->expects($this->once())
            ->method('createFieldDefinition')
            ->with($formDefinition)
            ->willReturn($fieldDefinition2);

        $fieldDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn('fieldName1');

        $fieldDefinition2->expects($this->once())
            ->method('getName')
            ->willReturn('fieldName2');

        $fieldDefinitionMap = $fieldListImplementation->createFieldDefinitionMap($formDefinition);

        $this->assertSame($fieldDefinition1, $fieldDefinitionMap['fieldName1']);
        $this->assertSame($fieldDefinition2, $fieldDefinitionMap['fieldName2']);
    }
}
