<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Finishers;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Fusion\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finisher\NullFinisher;
use PackageFactory\AtomicFusion\Forms\Fusion\Finishers\FinisherImplementation;

class FinisherImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function evaluatesToAFinisherDefinition()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation($fusionRuntime, '', '');

        $finisherDefinition = $finisherImplementation->evaluate();

        $this->assertTrue($finisherDefinition instanceof FinisherDefinitionInterface);
    }

    /**
     * @test
     */
    public function deliversName()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation($fusionRuntime, '', '');

        $fusionRuntime->expects($this->once())
            ->method('evaluate')
            ->with('/name', $finisherImplementation)
            ->willReturn('SomeName');

        $finisherDefinition = $finisherImplementation->evaluate();

        //
        // Check twice, call count should stay the same
        //
        $this->assertEquals('SomeName', $finisherDefinition->getName());
        $this->assertEquals('SomeName', $finisherDefinition->getName());
    }

    /**
     * @test
     */
    public function infersNameFromPath()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation1 = new FinisherImplementation($fusionRuntime, '/path', '');
        $finisherImplementation2 = new FinisherImplementation($fusionRuntime, '/path<WithType>', '');

        $finisherDefinition1 = $finisherImplementation1->evaluate();
        $finisherDefinition2 = $finisherImplementation2->evaluate();

        $this->assertEquals('path', $finisherDefinition1->getName());
        $this->assertEquals('path', $finisherDefinition2->getName());
    }

    /**
     * @test
     */
    public function deliversImplementationClassName()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation($fusionRuntime, '', '');

        $fusionRuntime->expects($this->exactly(3))
            ->method('evaluate')
            ->withConsecutive(
                ['/implementationClassName', $finisherImplementation],
                ['/options', $finisherImplementation],
                ['/name', $finisherImplementation]
            )
            ->will($this->onConsecutiveCalls(
                NullFinisher::class,
                [],
                'SomeName'
            ));

        $finisherDefinition = $finisherImplementation->evaluate();

        //
        // Check twice, call count should stay the same
        //
        $this->assertEquals(NullFinisher::class, $finisherDefinition->getImplementationClassName());
        $this->assertEquals(NullFinisher::class, $finisherDefinition->getImplementationClassName());
    }

    /**
     * @test
     */
    public function deliversOptions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $finisherImplementation = new FinisherImplementation($fusionRuntime, '', '');

        $fusionRuntime->expects($this->exactly(3))
            ->method('evaluate')
            ->withConsecutive(
                ['/implementationClassName', $finisherImplementation],
                ['/options', $finisherImplementation],
                ['/name', $finisherImplementation]
            )
            ->will($this->onConsecutiveCalls(
                NullFinisher::class,
                ['Some' => 'Options', 'SomeMore' => 'options'],
                'SomeName'
            ));

        $finisherDefinition = $finisherImplementation->evaluate();

        //
        // Check twice, call count should stay the same
        //
        $this->assertEquals(['Some' => 'Options', 'SomeMore' => 'options'], $finisherDefinition->getOptions());
        $this->assertEquals(['Some' => 'Options', 'SomeMore' => 'options'], $finisherDefinition->getOptions());
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

        $finisherDefinition = $finisherImplementation->evaluate();
        $finisherDefinition->getImplementationClassName();
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

        $finisherDefinition = $finisherImplementation->evaluate();
        $finisherDefinition->getImplementationClassName();
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

        $finisherDefinition = $finisherImplementation->evaluate();
        $finisherDefinition->getImplementationClassName();
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

        $finisherDefinition = $finisherImplementation->evaluate();
        $finisherDefinition->getImplementationClassName();
    }
}
