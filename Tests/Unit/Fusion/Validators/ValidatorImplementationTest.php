<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Validators;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Validation\Validator\StringValidator;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Validators\ValidatorImplementation;

class ValidatorImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsValidatorDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $validatorImplementation = new ValidatorImplementation($fusionRuntime, '', '');

        $fusionRuntime->expects($this->exactly(4))
            ->method('evaluate')
            ->withConsecutive(
                ['/name', $validatorImplementation],
                ['/implementationClassName', $validatorImplementation],
                ['/options', $validatorImplementation],
                ['/message', $validatorImplementation]
            )
            ->will($this->onConsecutiveCalls(
                'SomeName',
                StringValidator::class,
                [],
                'SomeCustomErrorMessage'
            ));

        $validatorDefinition = $validatorImplementation->evaluate();

        $this->assertTrue($validatorDefinition instanceof ValidatorDefinitionInterface);
        $this->assertEquals('SomeName', $validatorDefinition->getName());
        $this->assertEquals(StringValidator::class, $validatorDefinition->getImplementationClassName());
        $this->assertEquals([], $validatorDefinition->getOptions());
        $this->assertEquals('SomeCustomErrorMessage', $validatorDefinition->getCustomErrorMessage());
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477754469
     */
    public function complainsIfUsedDirectly()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $validatorImplementation = new ValidatorImplementation(
            $fusionRuntime,
            '',
            'PackageFactory.AtomicFusion.Forms:Validator'
        );

        $validatorImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477754482
     */
    public function complainsIfImplementationClassNameIsNotSet()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $validatorImplementation = new ValidatorImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $validatorImplementation, '']
        ]));

        $validatorImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477754490
     */
    public function complainsIfImplementationClassDoesNotExist()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $validatorImplementation = new ValidatorImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $validatorImplementation, 'Some\\NonExistent\\Class']
        ]));

        $validatorImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477754498
     */
    public function complainsIfImplementationClassIsOfWrongType()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $validatorImplementation = new ValidatorImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $validatorImplementation, FusionRuntime::class]
        ]));

        $validatorImplementation->evaluate();
    }

    /**
     * @test
     * @expectedException PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException
     * @expectedExceptionCode 1477754506
     */
    public function complainsIfOptionsIsOfWrongType()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $validatorImplementation = new ValidatorImplementation(
            $fusionRuntime,
            '',
            ''
        );

        $fusionRuntime->method('evaluate')->will($this->returnValueMap([
            ['/implementationClassName', $validatorImplementation, StringValidator::class],
            ['/options', $validatorImplementation, 'SomeString']
        ]));

        $validatorImplementation->evaluate();
    }
}
