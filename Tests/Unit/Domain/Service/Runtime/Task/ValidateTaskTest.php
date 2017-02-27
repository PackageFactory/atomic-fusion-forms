<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime\Task;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Error\Messages\Result;
use Neos\Error\Messages\Error;
use Neos\Flow\Validation\Validator\ValidatorInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\ValidatorResolverInterface;
use PackageFactory\AtomicFusion\Forms\Factory\MessageFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task\ValidateTask;

class ValidateTaskTest extends UnitTestCase
{
    /**
     * @test
     */
    public function resolvesValidatorsFromFieldDefinition()
    {
        $validatorDefinition1 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition2 = $this->createMock(ValidatorDefinitionInterface::class);

        $validationResult = $this->createMock(Result::class);
        $validationResult->method('hasErrors')->willReturn(false);
        $validationResult->method('forProperty')->willReturn($validationResult);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn($validationResult);

        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition->method('getValidatorDefinitions')->willReturn([$validatorDefinition1, $validatorDefinition2]);

        //
        // Expect, that the validator resolver gets called exactly two times with the
        // validator definitions returned by the field definition
        //
        $validatorResolver = $this->createMock(ValidatorResolverInterface::class);
        $validatorResolver->expects($this->exactly(2))
            ->method('resolve')
            ->withConsecutive(
                [$this->identicalTo($validatorDefinition1)],
                [$this->identicalTo($validatorDefinition2)]
            )
            ->willReturn($validator);

        $validateTask = new ValidateTask();
        $this->inject($validateTask, 'validatorResolver', $validatorResolver);

        $validateTask->run($fieldDefinition, '', $validationResult);
    }

    /**
     * @test
     */
    public function runsValidatorsAndAddsValidationResultToThePassedResultObject()
    {
        $validatorDefinition1 = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition2 = $this->createMock(ValidatorDefinitionInterface::class);

        $validationResult1 = $this->createMock(Result::class);
        $validationResult1->method('hasErrors')->willReturn(false);
        $validationResult2 = $this->createMock(Result::class);
        $validationResult2->method('hasErrors')->willReturn(false);

        //
        // Expect both validators to be called exactly once
        //
        $validator1 = $this->createMock(ValidatorInterface::class);
        $validator1->expects($this->once())->method('validate')->with('TheValue')->willReturn($validationResult1);
        $validator2 = $this->createMock(ValidatorInterface::class);
        $validator2->expects($this->once())->method('validate')->with('TheValue')->willReturn($validationResult2);

        $globalValidationResult = $this->createMock(Result::class);
        $globalValidationResult->method('hasErrors')->willReturn(false);

        //
        // Expect, that the global validation result will receive 2 messages
        //
        $globalValidationResult->expects($this->exactly(2))
            ->method('merge')
            ->withConsecutive(
                [$this->identicalTo($validationResult1)],
                [$this->identicalTo($validationResult2)]
            );

        //
        // Expect, that the global validation result will be requested to return the subresult
        // for the current field exactly two times
        //
        $globalValidationResult->expects($this->exactly(2))
            ->method('forProperty')
            ->with('TheFieldName')
            ->willReturn($globalValidationResult);

        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition->method('getValidatorDefinitions')->willReturn([$validatorDefinition1, $validatorDefinition2]);
        $fieldDefinition->method('getName')->willReturn('TheFieldName');

        $validatorResolver = $this->createMock(ValidatorResolverInterface::class);
        $validatorResolver->method('resolve')
            ->withConsecutive(
                [$validatorDefinition1],
                [$validatorDefinition2]
            )
            ->will($this->onConsecutiveCalls($validator1, $validator2));

        $validateTask = new ValidateTask();
        $this->inject($validateTask, 'validatorResolver', $validatorResolver);

        $validateTask->run($fieldDefinition, 'TheValue', $globalValidationResult);
    }

    /**
     * @test
     */
    public function addsCustomErrorMessageInCaseOneIsConfigured()
    {
        $validatorDefinition = $this->createMock(ValidatorDefinitionInterface::class);
        $validatorDefinition->method('hasCustomErrorMessage')->willReturn(true);
        $validatorDefinition->method('getCustomErrorMessage')->willReturn('TheCustomErrorMessage');

        $validationResult = $this->createMock(Result::class);
        $validationResult->method('hasErrors')->willReturn(true);
        $validationResult->method('forProperty')->willReturn($validationResult);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')->willReturn($validationResult);

        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition->method('getValidatorDefinitions')->willReturn([$validatorDefinition]);

        $validatorResolver = $this->createMock(ValidatorResolverInterface::class);
        $validatorResolver->method('resolve')
            ->willReturn($validator);

        //
        // Expect that a custom error message will be created, receiving the message
        // as configured in validator definition
        //
        $customErrorMessage = $this->createMock(Error::class);

        $messageFactory = $this->createMock(MessageFactory::class);
        $messageFactory->expects($this->once())
            ->method('createError')
            ->with('TheCustomErrorMessage')
            ->willReturn($customErrorMessage);

        //
        // Expect that the custom error message will be merged with the global
        // validation result
        //
        $validationResult->expects($this->once())
            ->method('addError')
            ->with($this->identicalTo($customErrorMessage));

        $validateTask = new ValidateTask();
        $this->inject($validateTask, 'validatorResolver', $validatorResolver);
        $this->inject($validateTask, 'messageFactory', $messageFactory);

        $validateTask->run($fieldDefinition, '', $validationResult);
    }
}
