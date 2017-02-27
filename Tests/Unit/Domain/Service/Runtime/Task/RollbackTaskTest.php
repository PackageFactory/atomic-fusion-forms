<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime\Task;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Flow\Error\Result;
use Neos\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Processor\ProcessorInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\ProcessorResolverInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task\RollbackTask;

class RollbackTaskTest extends UnitTestCase
{
    /**
     * @test
     */
    public function resolvesProcessorFromFieldDefinition()
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

        $rollbackTask = new RollbackTask();
        $this->inject($rollbackTask, 'processorResolver', $processorResolver);

        $rollbackTask->run(
            $propertyMappingConfiguration,
            $fieldDefinition,
            '',
            '',
            $result
        );
    }

    /**
     * @test
     */
    public function rollsResolvedProcessorBack()
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

        $rollbackTask = new RollbackTask();
        $this->inject($rollbackTask, 'processorResolver', $processorResolver);

        //
        // Main expectation
        //
        $processor->expects($this->once())->method('rollback')
            ->with(
                $this->identicalTo($fieldPropertyMappingConfiguration),
                $this->identicalTo($fieldResult),
                $this->identicalTo($fieldDefinition),
                $this->equalTo(['Option1', 'Option2']),
                $this->equalTo('TheUserInput'),
                $this->equalTo('TheProcessedValue')
            );

        $rollbackTask->run(
            $propertyMappingConfiguration,
            $fieldDefinition,
            'TheUserInput',
            'TheProcessedValue',
            $result
        );
    }
}
