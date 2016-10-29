<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Finishers;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finisher\NullFinisher;
use PackageFactory\AtomicFusion\Forms\Fusion\Finishers\FinisherImplementation;

class FinisherImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsFinisherDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation($fusionRuntime, '', '');

        $fusionRuntime->expects($this->exactly(3))
            ->method('evaluate')
            ->withConsecutive(
                ['/name', $finisherImplementation],
                ['/implementationClassName', $finisherImplementation],
                ['/options', $finisherImplementation]
            )
            ->will($this->onConsecutiveCalls(
                'SomeName',
                NullFinisher::class,
                []
            ));

        $finisherDefinition = $finisherImplementation->evaluate();

        $this->assertTrue($finisherDefinition instanceof FinisherDefinitionInterface);
        $this->assertEquals('SomeName', $finisherDefinition->getName());
        $this->assertEquals(NullFinisher::class, $finisherDefinition->getImplementationClassName());
        $this->assertEquals([], $finisherDefinition->getOptions());
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477759274
     */
    public function complainsIfUsedDirectly()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation(
            $fusionRuntime,
            '',
            'PackageFactory.AtomicFusion.Forms:Finisher'
        );

        $finisherImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477759281
     */
    public function complainsIfImplementationClassNameIsNotSet()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $finisherImplementation, '']
        ]));

        $finisherImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477759289
     */
    public function complainsIfImplementationClassDoesNotExist()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $finisherImplementation, 'Some\\NonExistent\\Class']
        ]));

        $finisherImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477759298
     */
    public function complainsIfImplementationClassIsOfWrongType()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $finisherImplementation, FusionRuntime::class]
        ]));

        $finisherImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477759306
     */
    public function complainsIfOptionsIsOfWrongType()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $finisherImplementation, NullFinisher::class],
            ['/options', $finisherImplementation, 'SomeString']
        ]));

        $finisherImplementation->evaluate();
    }
}
