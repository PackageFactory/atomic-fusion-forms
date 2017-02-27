<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Definition;

use Neos\Flow\Tests\UnitTestCase;
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
        $fieldDefinition = new FieldDefinition(['label' => 'SomeLabel']);

        $this->assertEquals('SomeLabel', $fieldDefinition->getLabel());
    }

    /**
     * @test
     */
    public function deliversName()
    {
        $fieldDefinition = new FieldDefinition(['name' => 'SomeName']);

        $this->assertEquals('SomeName', $fieldDefinition->getName());
    }

    /**
     * @test
     */
    public function deliversType()
    {
        $fieldDefinition = new FieldDefinition(['type' => 'SomeType']);

        $this->assertEquals('SomeType', $fieldDefinition->getType());
    }

    /**
     * @test
     */
    public function deliversPage()
    {
        $fieldDefinition = new FieldDefinition(['page' => 'SomePage']);

        $this->assertEquals('SomePage', $fieldDefinition->getPage());
    }

    /**
     * @test
     */
    public function deliversProcessorDefinition()
    {
        $processorDefinition = $this->createMock(ProcessorDefinitionInterface::class);
        $fieldDefinition = new FieldDefinition([]);

        $fieldDefinition->setProcessorDefinition($processorDefinition);

        $this->assertEquals($processorDefinition, $fieldDefinition->getProcessorDefinition());
    }

    /**
     * @test
     */
    public function deliversAllValidatorDefinitions()
    {
        $validatorDefinition1 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition1->method('getName')->willReturn('ValidatorDefinition1');
        $validatorDefinition2 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition2->method('getName')->willReturn('ValidatorDefinition2');
        $validatorDefinition3 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition3->method('getName')->willReturn('ValidatorDefinition3');

        $fieldDefinition = new FieldDefinition([]);

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
        $validatorDefinition1 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition1->method('getName')->willReturn('ValidatorDefinition1');
        $validatorDefinition2 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition2->method('getName')->willReturn('ValidatorDefinition2');
        $validatorDefinition3 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition3->method('getName')->willReturn('ValidatorDefinition3');

        $fieldDefinition = new FieldDefinition([]);

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
        $fieldDefinition = new FieldDefinition([]);

        $fieldDefinition->getValidatorDefinition('NonExistentValidatorDefinition');
    }

    /**
     * @test
     */
    public function deliversFormDefinition()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldDefinition = new FieldDefinition([]);

        $fieldDefinition->setFormDefinition($formDefinition);

        $this->assertEquals($formDefinition, $fieldDefinition->getFormDefinition());
    }
}
