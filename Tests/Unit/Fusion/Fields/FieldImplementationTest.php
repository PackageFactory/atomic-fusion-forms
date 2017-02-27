<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Fields;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Fusion\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Fields\FieldImplementation;

class FieldImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsFieldDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $fieldImplementation = new FieldImplementation($fusionRuntime, '', '');
        $processorDefinition = $this->createMock(ProcessorDefinitionInterface::class);
        $validatorDefinition = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition->method('getName')->willReturn('Validator');

        $fusionRuntime->expects($this->exactly(6))
            ->method('evaluate')
            ->withConsecutive(
                ['/label', $fieldImplementation],
                ['/name', $fieldImplementation],
                ['/type', $fieldImplementation],
                ['/page', $fieldImplementation],
                ['/processor', $fieldImplementation],
                ['/validators', $fieldImplementation]
            )
            ->will($this->onConsecutiveCalls(
                'SomeLabel',
                'SomeName',
                'SomeType',
                'SomePage',
                $processorDefinition,
                [$validatorDefinition]
            ));

        $fieldDefinition = $fieldImplementation->evaluate();

        $this->assertTrue($fieldDefinition instanceof FieldDefinitionInterface);
        $this->assertEquals('SomeLabel', $fieldDefinition->getLabel());
        $this->assertEquals('SomeName', $fieldDefinition->getName());
        $this->assertEquals('SomeType', $fieldDefinition->getType());
        $this->assertEquals('SomePage', $fieldDefinition->getPage());
        $this->assertSame($processorDefinition, $fieldDefinition->getProcessorDefinition());
        $this->assertEquals(1, count($fieldDefinition->getValidatorDefinitions()));
        $this->assertSame($validatorDefinition, $fieldDefinition->getValidatorDefinitions()['Validator']);
    }
}
