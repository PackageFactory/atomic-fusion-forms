<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Resolver;

use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\ProcessorResolver;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Processor\ProcessorInterface;

class ProcessorResolverTest extends ResolverTestCase
{
    const RESOLVER_TARGET_CLASS = ProcessorInterface::class;

    /**
     * @test
     */
    public function deliversProcessorObjectAccordingToProcessorDefinition()
    {
        $processorDefinition = $this->createMock(ProcessorDefinitionInterface::class);
        $processorDefinition->method('getImplementationClassName')->willReturn('SomeProcessor');

        $processorResolver = new ProcessorResolver();
        $this->injectObjectManager($processorResolver, ['SomeProcessor']);

        $processor = $processorResolver->resolve($processorDefinition);

        $this->assertEquals('SomeProcessor', $processor->getClassNameForTestPurposes());
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException
     * @expectedExceptionCode 1476599710
     */
    public function complainsIfRequestedProcessorDoesNotExist()
    {
        $processorDefinition = $this->createMock(ProcessorDefinitionInterface::class);
        $processorDefinition->method('getImplementationClassName')->willReturn('SomeProcessor');
        $processorDefinition->method('getOptions')->willReturn([]);

        $processorResolver = new ProcessorResolver();
        $this->injectObjectManager($processorResolver, []);

        $processor = $processorResolver->resolve($processorDefinition);

        $this->assertEquals('SomeProcessor', $processor->getClassNameForTestPurposes());
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException
     * @expectedExceptionCode 1476599826
     */
    public function complainsIfRequestedProcessorDoesNotImplementProcessorInterface()
    {
        $processorDefinition = $this->createMock(ProcessorDefinitionInterface::class);
        $processorDefinition->method('getImplementationClassName')->willReturn('SomeProcessor');
        $processorDefinition->method('getOptions')->willReturn([]);

        $processorResolver = new ProcessorResolver();
        $this->injectObjectManager($processorResolver, [], ['SomeProcessor']);

        $processor = $processorResolver->resolve($processorDefinition);

        $this->assertEquals('SomeProcessor', $processor->getClassNameForTestPurposes());
    }
}
