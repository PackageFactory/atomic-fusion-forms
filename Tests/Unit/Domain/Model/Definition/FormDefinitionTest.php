<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Definition;

use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinition;

class FormDefinitionTest extends UnitTestCase
{

    /**
     * @test
     */
    public function deliversLabel()
    {
        $formDefinition = new FormDefinition([
            'label' => 'SomeLabel'
        ]);

        $this->assertEquals('SomeLabel', $formDefinition->getLabel());
    }

    /**
     * @test
     */
    public function deliversName()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $this->assertEquals('SomeName', $formDefinition->getName());
    }

    /**
     * @test
     */
    public function deliversAction()
    {
        $formDefinition = new FormDefinition([
            'action' => 'SomeAction'
        ]);

        $this->assertEquals('SomeAction', $formDefinition->getAction());
    }

    /**
     * @test
     */
    public function deliversAllFieldDefinitions()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition1->method('getFormDefinition')->willReturn($formDefinition);
        $fieldDefinition1->method('getName')->willReturn('FieldDefinition1');

        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2->method('getFormDefinition')->willReturn($formDefinition);
        $fieldDefinition2->method('getName')->willReturn('FieldDefinition2');

        $fieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition3->method('getFormDefinition')->willReturn($formDefinition);
        $fieldDefinition3->method('getName')->willReturn('FieldDefinition3');

        $formDefinition->addFieldDefinition($fieldDefinition1);
        $formDefinition->addFieldDefinition($fieldDefinition2);
        $formDefinition->addFieldDefinition($fieldDefinition3);

        $this->assertEquals([
            'FieldDefinition1' => $fieldDefinition1,
            'FieldDefinition2' => $fieldDefinition2,
            'FieldDefinition3' => $fieldDefinition3
        ], $formDefinition->getFieldDefinitions());
    }

    /**
     * @test
     */
    public function deliversSingleFieldDefinitions()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition1->method('getFormDefinition')->willReturn($formDefinition);
        $fieldDefinition1->method('getName')->willReturn('FieldDefinition1');

        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2->method('getFormDefinition')->willReturn($formDefinition);
        $fieldDefinition2->method('getName')->willReturn('FieldDefinition2');

        $fieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition3->method('getFormDefinition')->willReturn($formDefinition);
        $fieldDefinition3->method('getName')->willReturn('FieldDefinition3');

        $formDefinition->addFieldDefinition($fieldDefinition1);
        $formDefinition->addFieldDefinition($fieldDefinition2);
        $formDefinition->addFieldDefinition($fieldDefinition3);

        $this->assertEquals('FieldDefinition1', $formDefinition->getFieldDefinition('FieldDefinition1')->getName());
        $this->assertEquals('FieldDefinition2', $formDefinition->getFieldDefinition('FieldDefinition2')->getName());
        $this->assertEquals('FieldDefinition3', $formDefinition->getFieldDefinition('FieldDefinition3')->getName());
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException
     * @expectedExceptionCode 1476536391
     */
    public function complainsIfTheRequestedFieldDefinitionDoesNotExist()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $formDefinition->getFieldDefinition('NonExistentFieldDefinition');
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException
     * @expectedExceptionCode 1476539967
     */
    public function complainsIfTheAddedFieldDefinitionDoesNotBelongToTheForm()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);
        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);

        $formDefinition->addFieldDefinition($fieldDefinition);
    }

    /**
     * @test
     */
    public function deliversAllFinisherDefinitions()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $finisherDefinition1 = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition1->method('getName')->willReturn('FinisherDefinition1');

        $finisherDefinition2 = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition2->method('getName')->willReturn('FinisherDefinition2');

        $finisherDefinition3 = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition3->method('getName')->willReturn('FinisherDefinition3');

        $formDefinition->addFinisherDefinition($finisherDefinition1);
        $formDefinition->addFinisherDefinition($finisherDefinition2);
        $formDefinition->addFinisherDefinition($finisherDefinition3);

        $this->assertEquals([
            'FinisherDefinition1' => $finisherDefinition1,
            'FinisherDefinition2' => $finisherDefinition2,
            'FinisherDefinition3' => $finisherDefinition3
        ], $formDefinition->getFinisherDefinitions());
    }

    /**
     * @test
     */
    public function deliversSingleFinisherDefinitions()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $finisherDefinition1 = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition1->method('getName')->willReturn('FinisherDefinition1');

        $finisherDefinition2 = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition2->method('getName')->willReturn('FinisherDefinition2');

        $finisherDefinition3 = $this->createMock(FinisherDefinitionInterface::class);
        $finisherDefinition3->method('getName')->willReturn('FinisherDefinition3');

        $formDefinition->addFinisherDefinition($finisherDefinition1);
        $formDefinition->addFinisherDefinition($finisherDefinition2);
        $formDefinition->addFinisherDefinition($finisherDefinition3);

        $this->assertEquals('FinisherDefinition1',
            $formDefinition->getFinisherDefinition('FinisherDefinition1')->getName());
        $this->assertEquals('FinisherDefinition2',
            $formDefinition->getFinisherDefinition('FinisherDefinition2')->getName());
        $this->assertEquals('FinisherDefinition3',
            $formDefinition->getFinisherDefinition('FinisherDefinition3')->getName());
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException
     * @expectedExceptionCode 1476536944
     */
    public function complainsIfTheRequestedFinisherDefinitionDoesNotExist()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $formDefinition->getFinisherDefinition('NonExistentFinisherDefinition');
    }

    /**
     * @test
     */
    public function deliversAllPageDefinitions()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $pageDefinition1 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition1->method('getFormDefinition')->willReturn($formDefinition);
        $pageDefinition1->method('getName')->willReturn('PageDefinition1');

        $pageDefinition2 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition2->method('getFormDefinition')->willReturn($formDefinition);
        $pageDefinition2->method('getName')->willReturn('PageDefinition2');

        $pageDefinition3 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition3->method('getFormDefinition')->willReturn($formDefinition);
        $pageDefinition3->method('getName')->willReturn('PageDefinition3');

        $formDefinition->addPageDefinition($pageDefinition1);
        $formDefinition->addPageDefinition($pageDefinition2);
        $formDefinition->addPageDefinition($pageDefinition3);

        $this->assertEquals([
            'PageDefinition1' => $pageDefinition1,
            'PageDefinition2' => $pageDefinition2,
            'PageDefinition3' => $pageDefinition3
        ], $formDefinition->getPageDefinitions());
    }

    /**
     * @test
     */
    public function deliversSinglePageDefinitions()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $pageDefinition1 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition1->method('getFormDefinition')->willReturn($formDefinition);
        $pageDefinition1->method('getName')->willReturn('PageDefinition1');

        $pageDefinition2 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition2->method('getFormDefinition')->willReturn($formDefinition);
        $pageDefinition2->method('getName')->willReturn('PageDefinition2');

        $pageDefinition3 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition3->method('getFormDefinition')->willReturn($formDefinition);
        $pageDefinition3->method('getName')->willReturn('PageDefinition3');

        $formDefinition->addPageDefinition($pageDefinition1);
        $formDefinition->addPageDefinition($pageDefinition2);
        $formDefinition->addPageDefinition($pageDefinition3);

        $this->assertEquals('PageDefinition1', $formDefinition->getPageDefinition('PageDefinition1')->getName());
        $this->assertEquals('PageDefinition2', $formDefinition->getPageDefinition('PageDefinition2')->getName());
        $this->assertEquals('PageDefinition3', $formDefinition->getPageDefinition('PageDefinition3')->getName());
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException
     * @expectedExceptionCode 1476536979
     */
    public function complainsIfTheRequestedPageDefinitionDoesNotExist()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $formDefinition->getPageDefinition('NonExistentPageDefinition');
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException
     * @expectedExceptionCode 1476540007
     */
    public function complainsIfTheAddedPageDefinitionDoesNotBelongToTheForm()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);
        $pageDefinition = $this->createMock(PageDefinitionInterface::class);

        $formDefinition->addPageDefinition($pageDefinition);
    }

    /**
     * @test
     */
    public function hasNoPagesInitially()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $this->assertFalse($formDefinition->hasPages());
    }

    /**
     * @test
     */
    public function hasPagesAfterAddingPageDefinitions()
    {
        $formDefinition = new FormDefinition([
            'name' => 'SomeName'
        ]);

        $pageDefinition = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition->method('getFormDefinition')->willReturn($formDefinition);

        $formDefinition->addPageDefinition($pageDefinition);

        $this->assertTrue($formDefinition->hasPages());
    }
}
