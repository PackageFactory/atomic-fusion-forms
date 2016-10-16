<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime;

use TYPO3\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormState;

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
}
