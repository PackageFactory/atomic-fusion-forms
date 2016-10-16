<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Resolver;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Object\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\FinisherResolver;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finishers\FinisherInterface;

class FinisherResolverTest extends UnitTestCase
{
    /**
     * Inject an object manager that will always return the string class name for a requested
     * finisher.
     *
     * @param FinisherResolver $finisherResolver
     * @param array $validFinisherClassNames
     * @return void
     */
    protected function injectObjectManager(
        FinisherResolver $finisherResolver,
        array $validFinisherClassNames = [],
        array $invalidFinisherClassNames = []
    )
    {
        $reflectionService = $this->createMock(ReflectionService::class);
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $returnMap = [];

        $returnMap[] = [ReflectionService::class, $reflectionService];

        if (current($validFinisherClassNames) instanceof FinisherInterface) {
            $reflectionService->method('getAllImplementationClassNamesForInterface')
                ->with(FinisherInterface::class)->willReturn(array_keys($validFinisherClassNames));
        } else {
            $reflectionService->method('getAllImplementationClassNamesForInterface')
                ->with(FinisherInterface::class)->willReturn($validFinisherClassNames);
        }

        foreach ($validFinisherClassNames as $key => $value) {
            if ($value instanceof FinisherInterface) {
                $finisher = $value;
                $validFinisherClassName = $key;
            } else {
                $finisher = $this->getMockBuilder(FinisherInterface::class)
                    ->setMethods(['getClassNameForTestPurposes', 'execute'])
                    ->getMock();
                $finisher->method('getClassNameForTestPurposes')->willReturn($value);

                $validFinisherClassName = $value;
            }

            $returnMap[] = [$validFinisherClassName, $finisher];
        }

        foreach ($invalidFinisherClassNames as $invalidFinisherClassName) {
            $returnMap[] = [$invalidFinisherClassName, new \stdClass];
        }

        $objectManager->method('isRegistered')->will($this->returnCallback(
            function ($name) use ($validFinisherClassNames, $invalidFinisherClassNames) {
                return in_array($name, $validFinisherClassNames) || array_key_exists($name, $validFinisherClassNames) ||
                    in_array($name, $invalidFinisherClassNames);
            }
        ));
        $objectManager->method('get')->will($this->returnValueMap($returnMap));

        $this->inject($finisherResolver, 'objectManager', $objectManager);
    }

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
}
