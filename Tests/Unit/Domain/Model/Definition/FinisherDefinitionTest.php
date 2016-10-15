<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Definition;

use TYPO3\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinition;

class FinisherDefinitionTest extends UnitTestCase {

    /**
     * @test
     */
    public function deliversImplementationClassName()
    {
        $finisherDefinition = new FinisherDefinition('SomeClassName', []);

        $this->assertEquals('SomeClassName', $finisherDefinition->getImplementationClassName());
    }

    /**
     * @test
     */
    public function deliversOptions()
    {
        $finisherDefinition = new FinisherDefinition('SomeClassName', [
            'option1',
            'option2',
            'option3'
        ]);

        $this->assertEquals([
            'option1',
            'option2',
            'option3'
        ], $finisherDefinition->getOptions());
    }
}
