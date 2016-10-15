<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Definition;

use TYPO3\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinition;

class ValidatorDefinitionTest extends UnitTestCase {

    /**
     * @test
     */
    public function deliversImplementationClassName()
    {
        $validatorDefinition = new ValidatorDefinition('SomeClassName', []);

        $this->assertEquals('SomeClassName', $validatorDefinition->getImplementationClassName());
    }

    /**
     * @test
     */
    public function deliversOptions()
    {
        $validatorDefinition = new ValidatorDefinition('SomeClassName', [
            'option1',
            'option2',
            'option3'
        ]);

        $this->assertEquals([
            'option1',
            'option2',
            'option3'
        ], $validatorDefinition->getOptions());
    }

    /**
     * @test
     */
    public function hasNoCustomErrorMessageInitially()
    {
        $validatorDefinition = new ValidatorDefinition('SomeClassName', []);

        $this->assertFalse($validatorDefinition->hasCustomErrorMessage());
    }

    /**
     * @test
     */
    public function deliversCustomErrorMessage()
    {
        $validatorDefinition = new ValidatorDefinition('SomeClassName', []);
        $validatorDefinition->setCustomErrorMessage('SomeErrorMessage');

        $this->assertEquals('SomeErrorMessage', $validatorDefinition->getCustomErrorMessage());
    }
}
