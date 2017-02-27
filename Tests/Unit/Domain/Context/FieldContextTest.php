<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Context;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Flow\Error\Result;
use Neos\Flow\Mvc\ActionRequest;
use PackageFactory\AtomicFusion\Forms\Domain\Context\FieldContext;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormStateInterface;

class FieldContextTest extends UnitTestCase
{
    /**
     * @test
     */
    public function shouldDeliverLabel()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);

        $formRuntime->expects($this->once())->method('getFormDefinition')->willReturn($formDefinition);
        $formDefinition->expects($this->once())
            ->method('getFieldDefinition')
            ->with('TheField')
            ->willReturn($fieldDefinition);
        $fieldDefinition->expects($this->once())->method('getLabel')->willReturn('TheLabel');

        $fieldContext = new FieldContext($formRuntime, 'TheField', '');

        $this->assertEquals('TheLabel', $fieldContext->getLabel());
    }

    /**
     * @test
     */
    public function shouldDeliverName()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $request = $this->createMock(ActionRequest::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);

        $formRuntime->expects($this->exactly(3))->method('getFormDefinition')->willReturn($formDefinition);
        $formRuntime->expects($this->exactly(3))->method('getRequest')->willReturn($request);
        $formDefinition->expects($this->exactly(3))
            ->method('getFieldDefinition')
            ->with('TheField')
            ->willReturn($fieldDefinition);
        $fieldDefinition->expects($this->exactly(3))->method('getName')->willReturn('TheName');

        $request->expects($this->exactly(3))->method('getArgumentNamespace')->willReturn('SomeArgumentNamespace');

        $fieldContext1 = new FieldContext($formRuntime, 'TheField', '');
        $fieldContext2 = new FieldContext($formRuntime, 'TheField', 'property');
        $fieldContext3 = new FieldContext($formRuntime, 'TheField', 'another.property');

        $this->assertEquals('SomeArgumentNamespace[TheName]', $fieldContext1->getName());
        $this->assertEquals('SomeArgumentNamespace[TheName][property]', $fieldContext2->getName());
        $this->assertEquals('SomeArgumentNamespace[TheName][another][property]', $fieldContext3->getName());
    }

    /**
     * @test
     */
    public function shouldDeliverArgument()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $formState->expects($this->exactly(2))
            ->method('getArgument')
            ->withConsecutive(['TheField'], ['TheField.the.property.path'])
            ->will($this->onConsecutiveCalls('TheArgument1', 'TheArgument2'));

        $formRuntime->method('getFormState')->willReturn($formState);

        $fieldContext1 = new FieldContext($formRuntime, 'TheField', '');
        $fieldContext2 = new FieldContext($formRuntime, 'TheField', 'the.property.path');

        $this->assertEquals('TheArgument1', $fieldContext1->getArgument());
        $this->assertEquals('TheArgument2', $fieldContext2->getArgument());
    }

    /**
     * @test
     */
    public function shouldDeliverValue()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $formState->expects($this->exactly(2))
            ->method('getValue')
            ->withConsecutive(['TheField'], ['TheField.the.property.path'])
            ->will($this->onConsecutiveCalls('TheValue1', 'TheValue2'));

        $formRuntime->method('getFormState')->willReturn($formState);

        $fieldContext1 = new FieldContext($formRuntime, 'TheField', '');
        $fieldContext2 = new FieldContext($formRuntime, 'TheField', 'the.property.path');

        $this->assertEquals('TheValue1', $fieldContext1->getValue());
        $this->assertEquals('TheValue2', $fieldContext2->getValue());
    }

    /**
     * @test
     */
    public function shouldDeliverValidationResult()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $validationResult = $this->createMock(Result::class);
        $validationResult->expects($this->exactly(2))
            ->method('forProperty')
            ->withConsecutive(['TheField'], ['TheField.the.property.path'])
            ->will($this->onConsecutiveCalls('TheValidationResult1', 'TheValidationResult2'));

        $formRuntime->method('getFormState')->willReturn($formState);
        $formState->method('getValidationResult')->willReturn($validationResult);

        $fieldContext1 = new FieldContext($formRuntime, 'TheField', '');
        $fieldContext2 = new FieldContext($formRuntime, 'TheField', 'the.property.path');

        $this->assertEquals('TheValidationResult1', $fieldContext1->getValidationResult());
        $this->assertEquals('TheValidationResult2', $fieldContext2->getValidationResult());
    }

    /**
     * @test
     */
    public function shouldDeliverHasErrors()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $validationResult = $this->createMock(Result::class);
        $validationResult->expects($this->exactly(2))
            ->method('forProperty')
            ->withConsecutive(['TheField'], ['TheField.the.property.path'])
            ->willReturn($validationResult);
        $validationResult->expects($this->exactly(2))
            ->method('hasErrors')
            ->will($this->onConsecutiveCalls(true, false));

        $formRuntime->method('getFormState')->willReturn($formState);
        $formState->method('getValidationResult')->willReturn($validationResult);

        $fieldContext1 = new FieldContext($formRuntime, 'TheField', '');
        $fieldContext2 = new FieldContext($formRuntime, 'TheField', 'the.property.path');

        $this->assertEquals(true, $fieldContext1->getHasErrors());
        $this->assertEquals(false, $fieldContext2->getHasErrors());
    }

    /**
     * @test
     */
    public function allowsCallOfRelevantMethods()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $fieldContext = new FieldContext($formRuntime, '', '');

        $this->assertTrue($fieldContext->allowsCallOfMethod('getLabel'));
        $this->assertTrue($fieldContext->allowsCallOfMethod('getName'));
        $this->assertTrue($fieldContext->allowsCallOfMethod('getArgument'));
        $this->assertTrue($fieldContext->allowsCallOfMethod('getValue'));
        $this->assertTrue($fieldContext->allowsCallOfMethod('getValidationResult'));
        $this->assertTrue($fieldContext->allowsCallOfMethod('getHasErrors'));
    }
}
