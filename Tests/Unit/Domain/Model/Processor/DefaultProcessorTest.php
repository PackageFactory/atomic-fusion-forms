<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Processor;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Error\Messages\Result;
use Neos\Flow\Property\PropertyMapper;
use Neos\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Factory\PropertyMapperFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Processor\DefaultProcessor;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;

class DefaultProcessorTest extends UnitTestCase
{
    /**
     * @test
     */
    public function invokesPropertyMapperWhenFieldDefinitionDeliversType()
    {
        $message = $this->createMock(Result::class);
        $result = $this->createMock(Result::class);
        $propertyMappingConfiguration = $this->createMock(PropertyMappingConfiguration::class);
        $propertyMapper = $this->createMock(PropertyMapper::class);
        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);

        $propertyMapperFactory = $this->createMock(PropertyMapperFactory::class);
        $propertyMapperFactory->method('createPropertyMapper')->willReturn($propertyMapper);

        $defaultProcessor = new DefaultProcessor();
        $this->inject($defaultProcessor, 'propertyMapperFactory', $propertyMapperFactory);

        $propertyMapper->expects($this->once())
            ->method('convert')
            ->with(
                $this->equalTo('SomeInputValue'),
                $this->equalTo('SomeType'),
                $this->equalTo($propertyMappingConfiguration)
            )
            ->willReturn('SomeOutputValue');

        $propertyMapper->expects($this->once())
            ->method('getMessages')
            ->willReturn($message);

        $fieldDefinition->expects($this->once())
            ->method('getType')
            ->willReturn('SomeType');

        $result->expects($this->once())
            ->method('merge')
            ->with($this->equalTo($message));

        $this->assertEquals('SomeOutputValue', $defaultProcessor->apply(
            $propertyMappingConfiguration,
            $result,
            $fieldDefinition,
            [],
            'SomeInputValue'
        ));
    }

    /**
     * @test
     */
    public function leavesInputValueUntouchedIfFieldDefinitionDeliversNoType()
    {

        $result = $this->createMock(Result::class);
        $propertyMappingConfiguration = $this->createMock(PropertyMappingConfiguration::class);
        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);

        $defaultProcessor = new DefaultProcessor();

        $this->assertEquals('AAA', $defaultProcessor->apply(
            $propertyMappingConfiguration,
            $result,
            $fieldDefinition,
            [],
            'AAA'
        ));
        $this->assertEquals('BBB', $defaultProcessor->apply(
            $propertyMappingConfiguration,
            $result,
            $fieldDefinition,
            [],
            'BBB'
        ));
        $this->assertEquals('CCC', $defaultProcessor->apply(
            $propertyMappingConfiguration,
            $result,
            $fieldDefinition,
            [],
            'CCC'
        ));
    }
}
