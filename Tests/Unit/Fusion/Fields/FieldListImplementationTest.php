<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Fields;

use Neos\Flow\Tests\UnitTestCase;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Fields\FieldListImplementation;

class FieldListImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsListsOfFieldDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $fieldListImplementation = new FieldListImplementation($fusionRuntime, '', '');

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
                $fieldDefinition1,
                $fieldDefinition2
            ));

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

    /**
     * @test
     */
    public function doesNotRequireExplicitFusionObjectAssignmentsForFields()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $fieldListImplementation = new FieldListImplementation($fusionRuntime, '', '');

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
                $fieldDefinition1,
                $fieldDefinition2
            ));

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
