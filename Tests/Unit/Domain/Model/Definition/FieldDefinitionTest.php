<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Definition;

use TYPO3\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinition;

class FieldDefinitionTest extends UnitTestCase
{

    /**
     * @test
     */
    public function deliversLabel()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldDefinition = new FieldDefinition([
            'label' => 'SomeLabel'
        ], $formDefinition);

        $this->assertEquals('SomeLabel', $fieldDefinition->getLabel());
    }

    /**
     * @test
     */
    public function deliversName()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldDefinition = new FieldDefinition([
            'name' => 'SomeName'
        ], $formDefinition);

        $this->assertEquals('SomeName', $fieldDefinition->getName());
    }

    /**
     * @test
     */
    public function deliversType()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldDefinition = new FieldDefinition([
            'type' => 'SomeType'
        ], $formDefinition);

        $this->assertEquals('SomeType', $fieldDefinition->getType());
    }

    /**
     * @test
     */
    public function deliversPage()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldDefinition = new FieldDefinition([
            'page' => 'SomePage'
        ], $formDefinition);

        $this->assertEquals('SomePage', $fieldDefinition->getPage());
    }

    /**
     * @test
     */
    public function deliversProcessorDefinition()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $processorDefinition = $this->createMock(ProcessorDefinitionInterface::class);
        $fieldDefinition = new FieldDefinition([
            'page' => 'SomePage'
        ], $formDefinition);

        $fieldDefinition->setProcessorDefinition($processorDefinition);

        $this->assertEquals($processorDefinition, $fieldDefinition->getProcessorDefinition());
    }

    /**
     * @test
     */
    public function deliversAllValidatorDefinitions()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

        $validatorDefinition1 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition1->method('getName')->willReturn('ValidatorDefinition1');
        $validatorDefinition2 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition2->method('getName')->willReturn('ValidatorDefinition2');
        $validatorDefinition3 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition3->method('getName')->willReturn('ValidatorDefinition3');

        $fieldDefinition = new FieldDefinition([
            'page' => 'SomePage'
        ], $formDefinition);

        $fieldDefinition->addValidatorDefinition($validatorDefinition1);
        $fieldDefinition->addValidatorDefinition($validatorDefinition2);
        $fieldDefinition->addValidatorDefinition($validatorDefinition3);

        $this->assertEquals([
            'ValidatorDefinition1' => $validatorDefinition1,
            'ValidatorDefinition2' => $validatorDefinition2,
            'ValidatorDefinition3' => $validatorDefinition3,
        ], $fieldDefinition->getValidatorDefinitions());
    }

    /**
     * @test
     */
    public function deliversSingleValidatorDefinition()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

        $validatorDefinition1 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition1->method('getName')->willReturn('ValidatorDefinition1');
        $validatorDefinition2 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition2->method('getName')->willReturn('ValidatorDefinition2');
        $validatorDefinition3 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition3->method('getName')->willReturn('ValidatorDefinition3');

        $fieldDefinition = new FieldDefinition([
            'page' => 'SomePage'
        ], $formDefinition);

        $fieldDefinition->addValidatorDefinition($validatorDefinition1);
        $fieldDefinition->addValidatorDefinition($validatorDefinition2);
        $fieldDefinition->addValidatorDefinition($validatorDefinition3);

        $this->assertEquals('ValidatorDefinition1',
            $fieldDefinition->getValidatorDefinition('ValidatorDefinition1')->getName());
        $this->assertEquals('ValidatorDefinition2',
            $fieldDefinition->getValidatorDefinition('ValidatorDefinition2')->getName());
        $this->assertEquals('ValidatorDefinition3',
            $fieldDefinition->getValidatorDefinition('ValidatorDefinition3')->getName());
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException
     * @expectedExceptionCode 1476539849
     */
    public function complainsIfTheRequestedValidatorDefinitionDoesNotExist()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

        $fieldDefinition = new FieldDefinition([
            'page' => 'SomePage'
        ], $formDefinition);

        $fieldDefinition->getValidatorDefinition('NonExistentValidatorDefinition');
    }

    /**
     * @test
     */
    public function deliversFormDefinition()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldDefinition = new FieldDefinition([
            'page' => 'SomePage'
        ], $formDefinition);

        $this->assertEquals($formDefinition, $fieldDefinition->getFormDefinition());
    }
}
