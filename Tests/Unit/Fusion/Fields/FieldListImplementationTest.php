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
use PackageFactory\AtomicFusion\Forms\Fusion\Fields\FieldListImplementation;

class FieldListImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function evaluatesToAFormDefinitionMapFactory()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $fieldListImplementation = new FieldListImplementation($fusionRuntime, '', '');

        $this->assertTrue($fieldListImplementation->evaluate() instanceof FieldDefinitionMapFactoryInterface);
    }

    /**
     * @test
     */
    public function createsListsOfFieldDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldListImplementation = new FieldListImplementation($fusionRuntime, '', '');

        $fieldDefinitionFactory1 = $this->createMock(FieldDefinitionFactoryInterface::class);
        $fieldDefinitionFactory2 = $this->createMock(FieldDefinitionFactoryInterface::class);
        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);

        $this->inject($fieldListImplementation, 'properties', [
            'field1' => [
                '__objectType' => 'SomeFusionObject'
            ],
            'field2' => [
                '__objectType' => 'SomeOtherFusionObject'
            ]
        ]);

        $fusionRuntime->expects($this->exactly(2))
            ->method('render')
            ->withConsecutive(['/field1'], ['/field2'])
            ->will($this->onConsecutiveCalls(
                $fieldDefinitionFactory1,
                $fieldDefinitionFactory2
            ));

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

    /**
     * @test
     */
    public function doesNotRequireExplicitFusionObjectAssignmentsForFields()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldListImplementation = new FieldListImplementation($fusionRuntime, '', '');

        $fieldDefinitionFactory1 = $this->createMock(FieldDefinitionFactoryInterface::class);
        $fieldDefinitionFactory2 = $this->createMock(FieldDefinitionFactoryInterface::class);
        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);

        $this->inject($fieldListImplementation, 'properties', [
            'field1' => [],
            'field2' => []
        ]);

        $fusionRuntime->expects($this->exactly(2))
            ->method('render')
            ->withConsecutive(
                ['/field1<PackageFactory.AtomicFusion.Forms:Field>'],
                ['/field2<PackageFactory.AtomicFusion.Forms:Field>']
            )
            ->will($this->onConsecutiveCalls(
                $fieldDefinitionFactory1,
                $fieldDefinitionFactory2
            ));

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
