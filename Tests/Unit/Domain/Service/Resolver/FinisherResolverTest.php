<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Resolver;

use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\FinisherResolver;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finishers\FinisherInterface;

class FinisherResolverTest extends ResolverTestCase
{
    const RESOLVER_TARGET_CLASS = FinisherInterface::class;

    /**
     * @test
     */
    public function deliversFinisherObjectAccordingToFinisherDefinition()
    {
        $finisherDefinition = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition->method('getImplementationClassName')->willReturn('SomeFinisher');
        $finisherDefinition->method('getOptions')->willReturn([]);

        $finisherResolver = new FinisherResolver();
        $this->injectObjectManager($finisherResolver, ['SomeFinisher']);

        $finisher = $finisherResolver->resolve($finisherDefinition);

        $this->assertEquals('SomeFinisher', $finisher->getClassNameForTestPurposes());
    }

    /**
     * @test
     */
    public function appliesOptionsToNewlyCreatedFinishers()
    {
        $finisherDefinition = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition->method('getImplementationClassName')->willReturn('SomeFinisher');
        $finisherDefinition->method('getOptions')->willReturn([
            'option1' => 'Value1',
            'option2' => 'Value2',
            'option3' => 'Value3'
        ]);

        $finisher = $this->getMockBuilder(FinisherInterface::class)
            ->setMethods([
                'setOption1',
                'setOption2',
                'setOption3',
                'execute'
            ])
            ->getMock();

        $finisher->expects($this->once())->method('setOption1')->with('Value1');
        $finisher->expects($this->once())->method('setOption2')->with('Value2');
        $finisher->expects($this->once())->method('setOption3')->with('Value3');

        $finisherResolver = new FinisherResolver();
        $this->injectObjectManager($finisherResolver, ['SomeFinisher' => $finisher]);

        $finisher = $finisherResolver->resolve($finisherDefinition);
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException
     * @expectedExceptionCode 1476611095
     */
    public function complainsIfRequestedFinisherDoesNotExist()
    {
        $finisherDefinition = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition->method('getImplementationClassName')->willReturn('SomeFinisher');
        $finisherDefinition->method('getOptions')->willReturn([]);

        $finisherResolver = new FinisherResolver();
        $this->injectObjectManager($finisherResolver, []);

        $finisher = $finisherResolver->resolve($finisherDefinition);

        $this->assertEquals('SomeFinisher', $finisher->getClassNameForTestPurposes());
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException
     * @expectedExceptionCode 1476611105
     */
    public function complainsIfRequestedFinisherDoesNotImplementFinisherInterface()
    {
        $finisherDefinition = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition->method('getImplementationClassName')->willReturn('SomeFinisher');
        $finisherDefinition->method('getOptions')->willReturn([]);

        $finisherResolver = new FinisherResolver();
        $this->injectObjectManager($finisherResolver, [], ['SomeFinisher']);

        $finisher = $finisherResolver->resolve($finisherDefinition);

        $this->assertEquals('SomeFinisher', $finisher->getClassNameForTestPurposes());
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException
     * @expectedExceptionCode 1476624534
     */
    public function complainsIfConfiguredOptionDoesNotExist()
    {
        $finisherDefinition = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition->method('getImplementationClassName')->willReturn('SomeFinisher');
        $finisherDefinition->method('getOptions')->willReturn([
            'option1' => 'Value1',
            'option2' => 'Value2'
        ]);

        $finisher = $this->getMockBuilder(FinisherInterface::class)
            ->setMethods([
                'setOption1',
                'execute'
            ])
            ->getMock();

        $finisherResolver = new FinisherResolver();
        $this->injectObjectManager($finisherResolver, ['SomeFinisher' => $finisher]);

        $finisherResolver->resolve($finisherDefinition);
    }
}
