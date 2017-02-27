<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Processors;

use Neos\Flow\Tests\UnitTestCase;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Processor\DefaultProcessor;
use PackageFactory\AtomicFusion\Forms\Fusion\Processors\ProcessorImplementation;

class ProcessorImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsProcessorDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $processorImplementation = new ProcessorImplementation($fusionRuntime, '', '');

        $fusionRuntime->expects($this->exactly(2))
            ->method('evaluate')
            ->withConsecutive(
                ['/implementationClassName', $processorImplementation],
                ['/options', $processorImplementation]
            )
            ->will($this->onConsecutiveCalls(
                DefaultProcessor::class,
                []
            ));

        $processorDefinition = $processorImplementation->evaluate();

        $this->assertTrue($processorDefinition instanceof ProcessorDefinitionInterface);
        $this->assertEquals(DefaultProcessor::class, $processorDefinition->getImplementationClassName());
        $this->assertEquals([], $processorDefinition->getOptions());
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477740676
     */
    public function complainsIfUsedDirectly()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $processorImplementation = new ProcessorImplementation(
            $fusionRuntime,
            '',
            'PackageFactory.AtomicFusion.Forms:Processor'
        );

        $processorImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477740801
     */
    public function complainsIfImplementationClassNameIsNotSet()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $processorImplementation = new ProcessorImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $processorImplementation, '']
        ]));

        $processorImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477741130
     */
    public function complainsIfImplementationClassDoesNotExist()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $processorImplementation = new ProcessorImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $processorImplementation, 'Some\\NonExistent\\Class']
        ]));

        $processorImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477741143
     */
    public function complainsIfImplementationClassIsOfWrongType()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $processorImplementation = new ProcessorImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $processorImplementation, FusionRuntime::class]
        ]));

        $processorImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477741187
     */
    public function complainsIfOptionsIsOfWrongType()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $processorImplementation = new ProcessorImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $processorImplementation, DefaultProcessor::class],
            ['/options', $processorImplementation, 'SomeString']
        ]));

        $processorImplementation->evaluate();
    }
}
