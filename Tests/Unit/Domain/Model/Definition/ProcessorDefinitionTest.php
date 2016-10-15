<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Definition;

use TYPO3\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinition;

class ProcessorDefinitionTest extends UnitTestCase {

    /**
     * @test
     */
    public function deliversImplementationClassName() {
        $processorDefinition = new ProcessorDefinition('SomeClassName', []);

        $this->assertEquals('SomeClassName', $processorDefinition->getImplementationClassName());
    }

    /**
     * @test
     */
    public function deliversOptions()
    {
        $processorDefinition = new ProcessorDefinition('SomeClassName', [
            'option1',
            'option2',
            'option3'
        ]);

        $this->assertEquals([
            'option1',
            'option2',
            'option3'
        ], $processorDefinition->getOptions());
    }
}
