<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime;

use TYPO3\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormState;

interface __getterStub__1476736746 {
	public function getTest1();
	public function getTest2();
}

class FormStateTest extends UnitTestCase
{
    /**
     * @test
     */
    public function deliversArguments()
    {
        $formState = new FormState();

        $formState->setArguments([
            'Argument1',
            'Argument2',
            'Argument3'
        ]);

        $this->assertEquals([
            'Argument1',
            'Argument2',
            'Argument3'
        ], $formState->getArguments());
    }

    /**
     * @test
     */
    public function deliversCurrentPage()
    {
        $formState = new FormState();

        $formState->setCurrentPage('TheCurrentPage');

        $this->assertEquals('TheCurrentPage', $formState->getCurrentPage());
    }

    /**
     * @test
     */
    public function checksIfSomeGivenPageIsTheCurrentPage()
    {
        $formState = new FormState();

        $formState->setCurrentPage('TheCurrentPage');

        $this->assertFalse($formState->isCurrentPage('SomeOtherPage'));
        $this->assertTrue($formState->isCurrentPage('TheCurrentPage'));
    }

    /**
     * @test
     */
    public function changesInitialCallStateOnwakeup()
    {
        $formState = new FormState();

        $this->assertTrue($formState->isInitialCall());

        $nextFormState = unserialize(serialize($formState));

        $this->assertFalse($nextFormState->isInitialCall());
    }

    /**
     * @test
     */
    public function deliversArgumentsByPath()
    {
        $formState = new FormState();
        $this->inject($formState, 'arguments', [
            'toplevel' => 'Test1',
            'pretty' => [
                'deeply' => [
                    'nested' => 'Test2'
                ]
            ]
        ]);

        $this->assertEquals('Test1', $formState->getArgument('toplevel'));
        $this->assertEquals(['deeply' => [
            'nested' => 'Test2'
        ]], $formState->getArgument('pretty'));
        $this->assertEquals(['nested' => 'Test2'], $formState->getArgument('pretty.deeply'));
        $this->assertEquals('Test2', $formState->getArgument('pretty.deeply.nested'));
    }

    /**
     * @test
     */
    public function deliversValuesByPath()
    {
        $formState = new FormState();

        $stub1 = $this->createMock(__getterStub__1476736746::class);
        $stub2 = $this->createMock(__getterStub__1476736746::class);

        $stub1->method('getTest1')->willReturn($stub2);
        $stub2->method('getTest2')->willReturn('Test3');

        $this->inject($formState, 'values', [
            'toplevel' => 'Test1',
            'pretty' => [
                'deeply' => [
                    'nested' => 'Test2'
                ]
            ],
            'nested' => $stub1
        ]);

        $this->assertEquals('Test1', $formState->getValue('toplevel'));
        $this->assertEquals(['deeply' => [
            'nested' => 'Test2'
        ]], $formState->getValue('pretty'));
        $this->assertEquals(['nested' => 'Test2'], $formState->getValue('pretty.deeply'));
        $this->assertEquals('Test2', $formState->getValue('pretty.deeply.nested'));
        $this->assertEquals($stub1, $formState->getValue('nested'));
        $this->assertEquals($stub2, $formState->getValue('nested.test1'));
        $this->assertEquals('Test3', $formState->getValue('nested.test1.test2'));
    }

	/**
     * @test
     */
    public function mergesNewArgumentsWithExistingOnes()
    {
        $formState = new FormState();
        $this->inject($formState, 'arguments', [
			'Test1' => 'Value1',
			'Test2' => 'Value2',
			'Test4' => 'Value4'
		]);

        //
        // Expect that new arguments and existing arguments will be merged
        //
        $formState->mergeArguments([
			'Test2' => 'OverriddenValue',
			'Test3' => 'AddedValue'
		]);

        $this->assertEquals([
            'Test1' => 'Value1',
            'Test2' => 'OverriddenValue',
            'Test3' => 'AddedValue',
            'Test4' => 'Value4'
        ], $formState->getArguments());
    }
}
