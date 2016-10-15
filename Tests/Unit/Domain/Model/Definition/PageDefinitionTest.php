<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Definition;

use TYPO3\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException;

class PageDefinitionTest extends UnitTestCase {

    /**
     * @test
     */
    public function deliversLabel()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $pageDefinition = new PageDefinition([
            'label' => 'SomeLabel'
        ], $formDefinition);

        $this->assertEquals('SomeLabel', $pageDefinition->getLabel());
    }

    /**
     * @test
     */
    public function deliversName()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $pageDefinition = new PageDefinition([
            'name' => 'SomeName'
        ], $formDefinition);

        $this->assertEquals('SomeName', $pageDefinition->getName());
    }

    /**
     * @test
     */
    public function deliversFormDefinition()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $pageDefinition = new PageDefinition([], $formDefinition);

        $this->assertEquals($formDefinition, $pageDefinition->getFormDefinition());
    }

    /**
     * @test
     */
    public function deliversAllFieldDefinitionsDomesticToThePage()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

        $domesticFieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $domesticFieldDefinition1->method('getPage')->willReturn('ThePageDefinition');
        $domesticFieldDefinition1->method('getName')->willReturn('DomesticFieldDefinition1');

        $domesticFieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $domesticFieldDefinition2->method('getPage')->willReturn('ThePageDefinition');
        $domesticFieldDefinition2->method('getName')->willReturn('DomesticFieldDefinition2');

        $domesticFieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $domesticFieldDefinition3->method('getPage')->willReturn('ThePageDefinition');
        $domesticFieldDefinition3->method('getName')->willReturn('DomesticFieldDefinition3');

        $foreignFieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $foreignFieldDefinition->method('getPage')->willReturn('AnotherPageDefinition');

        $formDefinition->method('getFieldDefinitions')->willReturn([
            $domesticFieldDefinition1,
            $foreignFieldDefinition,
            $domesticFieldDefinition2,
            $foreignFieldDefinition,
            $domesticFieldDefinition3,
            $foreignFieldDefinition
        ]);

        $pageDefinition = new PageDefinition([
            'name' => 'ThePageDefinition'
        ], $formDefinition);

        $this->assertEquals([
            'DomesticFieldDefinition1' => $domesticFieldDefinition1,
            'DomesticFieldDefinition2' => $domesticFieldDefinition2,
            'DomesticFieldDefinition3' => $domesticFieldDefinition3
        ], $pageDefinition->getFieldDefinitions());
    }

    /**
     * @test
     */
    public function deliversSingleFieldDefinitionsDomesticToThePage()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

        $domesticFieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $domesticFieldDefinition1->method('getPage')->willReturn('ThePageDefinition');
        $domesticFieldDefinition1->method('getName')->willReturn('DomesticFieldDefinition1');

        $domesticFieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $domesticFieldDefinition2->method('getPage')->willReturn('ThePageDefinition');
        $domesticFieldDefinition2->method('getName')->willReturn('DomesticFieldDefinition2');

        $domesticFieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $domesticFieldDefinition3->method('getPage')->willReturn('ThePageDefinition');
        $domesticFieldDefinition3->method('getName')->willReturn('DomesticFieldDefinition3');

        $foreignFieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $foreignFieldDefinition->method('getPage')->willReturn('AnotherPageDefinition');

        $formDefinition->method('getFieldDefinitions')->willReturn([
            $domesticFieldDefinition1,
            $foreignFieldDefinition,
            $domesticFieldDefinition2,
            $foreignFieldDefinition,
            $domesticFieldDefinition3,
            $foreignFieldDefinition
        ]);

        $pageDefinition = new PageDefinition([
            'name' => 'ThePageDefinition'
        ], $formDefinition);

        $this->assertEquals('DomesticFieldDefinition1',
            $pageDefinition->getFieldDefinition('DomesticFieldDefinition1')->getName());
        $this->assertEquals('DomesticFieldDefinition2',
            $pageDefinition->getFieldDefinition('DomesticFieldDefinition2')->getName());
        $this->assertEquals('DomesticFieldDefinition3',
            $pageDefinition->getFieldDefinition('DomesticFieldDefinition3')->getName());
    }

    /**
     * @expectedException DefinitionException
     * @expectedExceptionCode 1476537396
     */
    public function complainsIfTheRequestedFieldDefinitionIsForeignToThePage()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

        $foreignFieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $foreignFieldDefinition->method('getPage')->willReturn('AnotherPageDefinition');
        $foreignFieldDefinition->method('getName')->willReturn('ForeignFieldDefinition');

        $formDefinition->method('getFieldDefinitions')->willReturn([
            $foreignFieldDefinition
        ]);

        $pageDefinition = new PageDefinition([
            'name' => 'ThePageDefinition'
        ], $formDefinition);

        $pageDefinition->getFieldDefinition('ForeignFieldDefinition');
    }

    /**
     * @expectedException DefinitionException
     * @expectedExceptionCode 1476537396
     */
    public function complainsIfTheRequestedFieldDefinitionIsDoesNotExist()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

        $formDefinition->method('getFieldDefinitions')->willReturn([]);

        $pageDefinition = new PageDefinition([
            'name' => 'ThePageDefinition'
        ], $formDefinition);

        $pageDefinition->getFieldDefinition('ForeignFieldDefinition');
    }
}
