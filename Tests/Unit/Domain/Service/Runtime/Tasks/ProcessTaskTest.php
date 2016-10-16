<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime\Tasks;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Processors\ProcessorInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\ProcessorResolverInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Tasks\ProcessTask;

class ProcessTaskTest extends UnitTestCase
{
    /**
     * @test
     */
    public function resolvesProcessorFromProcessorDefinition()
    {
        $result = $this->createMock(Result::class);
        $result->method('forProperty')->willReturn($result);

        $propertyMappingConfiguration = $this->createMock(PropertyMappingConfiguration::class);
        $propertyMappingConfiguration->method('forProperty')->willReturn($propertyMappingConfiguration);

        $processorDefinition = $this->createMock(ProcessorDefinitionInterface::class);
        $processorDefinition->method('getOptions')->willReturn([]);

        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition->method('getProcessorDefinition')->willReturn($processorDefinition);

        $processor = $this->createMock(ProcessorInterface::class);

        $processorResolver = $this->createMock(ProcessorResolverInterface::class);
        $processorResolver->expects($this->once())
            ->method('resolve')
            ->with($this->equalTo($processorDefinition))
            ->willReturn($processor);

        $processTask = new ProcessTask();
        $this->inject($processTask, 'processorResolver', $processorResolver);

        $processTask->run(
            $propertyMappingConfiguration,
            $fieldDefinition,
            '',
            $result
        );
    }

    /**
     * @test
     */
    public function runsResolvedProcessor()
    {
        $fieldResult = $this->createMock(Result::class);
        $result = $this->createMock(Result::class);
        $result->method('forProperty')->with('TheFieldName')->willReturn($fieldResult);

        $fieldPropertyMappingConfiguration = $this->createMock(PropertyMappingConfiguration::class);
        $propertyMappingConfiguration = $this->createMock(PropertyMappingConfiguration::class);
        $propertyMappingConfiguration->method('forProperty')->with('TheFieldName')
            ->willReturn($fieldPropertyMappingConfiguration);

        $processorDefinition = $this->createMock(ProcessorDefinitionInterface::class);
        $processorDefinition->method('getOptions')->willReturn(['Option1', 'Option2']);

        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition->method('getProcessorDefinition')->willReturn($processorDefinition);
        $fieldDefinition->method('getName')->willReturn('TheFieldName');

        $processor = $this->createMock(ProcessorInterface::class);

        $processorResolver = $this->createMock(ProcessorResolverInterface::class);
        $processorResolver->method('resolve')->with($processorDefinition)->willReturn($processor);

        $processTask = new ProcessTask();
        $this->inject($processTask, 'processorResolver', $processorResolver);

        //
        // Main expectation
        //
        $processor->expects($this->once())->method('apply')
            ->with(
                $this->equalTo($fieldPropertyMappingConfiguration),
                $this->equalTo($fieldResult),
                $this->equalTo($fieldDefinition),
                $this->equalTo(['Option1', 'Option2']),
                $this->equalTo('TheUserInput')
            );

        $processTask->run(
            $propertyMappingConfiguration,
            $fieldDefinition,
            'TheUserInput',
            $result
        );
    }
}
