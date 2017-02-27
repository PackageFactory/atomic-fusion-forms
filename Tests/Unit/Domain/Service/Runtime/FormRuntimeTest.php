<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Error\Messages\Result;
use Neos\Flow\Http\Response;
use Neos\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Factory\PropertyMappingConfigurationFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormStateInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\Factory\FormStateFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task\ProcessTaskInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task\ValidateTaskInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task\RollbackTaskInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task\FinishTaskInterface;

class FormRuntimeTest extends UnitTestCase
{
    /**
     * @test
     */
    public function deliversFormDefinition()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);

        $formRuntime = new FormRuntime($formDefinition, $request);

        $this->assertSame($formDefinition, $formRuntime->getFormDefinition());
    }

    /**
     * @test
     */
    public function deliversRequest()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $formDefinition->method('getName')->willReturn('TheForm');

        $request = $this->createMock(ActionRequest::class);
        $request->method('getPluginArguments')->willReturn([
            'TheForm' => [
                'Argument1' => 'Value1',
                'Argument2' => 'Value2'
            ]
        ]);

        $formRuntime = new FormRuntime($formDefinition, $request);
        $formRuntimeRequest = $formRuntime->getRequest();

        $this->assertNotSame($request, $formRuntimeRequest);
        $this->assertSame($request, $formRuntimeRequest->getParentRequest());
        $this->assertEquals([
            'Argument1' => 'Value1',
            'Argument2' => 'Value2'
        ], $formRuntimeRequest->getArguments());
    }

    /**
     * @test
     */
    public function deliversFormState()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $request = $this->createMock(ActionRequest::class);

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'formState', $formState);

        $this->assertSame($formState, $formRuntime->getFormState());
    }

    /**
     * @test
     */
    public function processesForm()
    {
        $processTask = $this->createMock(ProcessTaskInterface::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);
        $propertyMappingConfiguration = $this->createMock(PropertyMappingConfiguration::class);
        $validationResult = $this->createMock(Result::class);

        $formState = $this->createMock(FormStateInterface::class);
        $formState->method('getValidationResult')->willReturn($validationResult);

        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition1->method('getName')->willReturn('Field1');
        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2->method('getName')->willReturn('Field2');
        $fieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition3->method('getName')->willReturn('Field3');

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'processTask', $processTask);
        $this->inject($formRuntime, 'propertyMappingConfiguration', $propertyMappingConfiguration);
        $this->inject($formRuntime, 'formState', $formState);

        $formState->expects($this->exactly(3))
            ->method('getArgument')
            ->withConsecutive(['Field1'], ['Field2'], ['Field3'])
            ->will($this->onConsecutiveCalls('Input1', 'Input2', null));

        $formState->expects($this->exactly(3))
            ->method('addValue')
            ->withConsecutive(['Field1', 'Value1'], ['Field2', 'Value2'], ['Field3', null]);

        $formDefinition->method('hasPages')->willReturn(false);
        $formDefinition->method('getFieldDefinitions')
            ->willReturn([$fieldDefinition1, $fieldDefinition2, $fieldDefinition3]);

        //
        // Expect that the process task will be run three times
        //
        $processTask->expects($this->exactly(3))
            ->method('run')
            ->withConsecutive(
                [$propertyMappingConfiguration, $fieldDefinition1, 'Input1', $validationResult],
                [$propertyMappingConfiguration, $fieldDefinition2, 'Input2', $validationResult],
                [$propertyMappingConfiguration, $fieldDefinition3, null, $validationResult]
            )
            ->will($this->onConsecutiveCalls('Value1', 'Value2', null));

        $formRuntime->process();
    }

    /**
     * @test
     */
    public function validatesForm()
    {
        $validateTask = $this->createMock(ValidateTaskInterface::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);
        $validationResult = $this->createMock(Result::class);

        $formState = $this->createMock(FormStateInterface::class);
        $formState->method('getValidationResult')->willReturn($validationResult);

        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition1->method('getName')->willReturn('Field1');
        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2->method('getName')->willReturn('Field2');
        $fieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition3->method('getName')->willReturn('Field3');

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'validateTask', $validateTask);
        $this->inject($formRuntime, 'formState', $formState);

        $formState->expects($this->exactly(3))
            ->method('getValue')
            ->withConsecutive(['Field1'], ['Field2'], ['Field3'])
            ->will($this->onConsecutiveCalls('Value1', 'Value2', null));

        $formDefinition->method('hasPages')->willReturn(false);
        $formDefinition->method('getFieldDefinitions')
            ->willReturn([$fieldDefinition1, $fieldDefinition2, $fieldDefinition3]);

        //
        // Expect that the validate task will be run three times
        //
        $validateTask->expects($this->exactly(3))
            ->method('run')
            ->withConsecutive(
                [$fieldDefinition1, 'Value1', $validationResult],
                [$fieldDefinition2, 'Value2', $validationResult],
                [$fieldDefinition3, null, $validationResult]
            );

        $formRuntime->validate();
    }

    /**
     * @test
     */
    public function distinguishesBetweenPagedAndUnPagedForms()
    {
        $request = $this->createMock(ActionRequest::class);

        $class = new \ReflectionClass(FormRuntime::class);
        $getFieldDefinitionsForCurrentPage = $class->getMethod('getFieldDefinitionsForCurrentPage');
        $getFieldDefinitionsForCurrentPage->setAccessible(true);

        //
        // In first case, we will have no pages, so expectation is, that the global
        // field definitions will be returned
        //
        $formDefinition1 = $this->createMock(FormDefinitionInterface::class);
        $formRuntime1 = new FormRuntime($formDefinition1, $request);

        $formDefinition1->method('hasPages')->willReturn(false);
        $formDefinition1->expects($this->once())
            ->method('getFieldDefinitions')
            ->willReturn('GlobalFieldDefinitions');

        $this->assertEquals('GlobalFieldDefinitions', $getFieldDefinitionsForCurrentPage->invoke($formRuntime1));

        //
        // In second case we will have pages, so expectation is, that the page
        // specific field definitions for the current page will be returned
        //
        $formDefinition2 = $this->createMock(FormDefinitionInterface::class);
        $pageDefinition2 = $this->createMock(PageDefinitionInterface::class);
        $formState2 = $this->createMock(FormStateInterface::class);
        $formRuntime2 = new FormRuntime($formDefinition2, $request);
        $this->inject($formRuntime2, 'formState', $formState2);

        $formState2->method('getCurrentPage')->willReturn('TheCurrentPage');

        $formDefinition2->method('hasPages')->willReturn(true);
        $formDefinition2->expects($this->once())
            ->method('getPageDefinition')
            ->with($this->equalTo('TheCurrentPage'))
            ->willReturn($pageDefinition2);
        $formDefinition1->method('getFieldDefinitions')->willReturn('GlobalFieldDefinitions');

        $pageDefinition2->expects($this->once())
            ->method('getFieldDefinitions')
            ->willReturn('PageSpecificFieldDefinitions');

        $result = $getFieldDefinitionsForCurrentPage->invoke($formRuntime2);

        $this->assertNotEquals('GlobalFieldDefinitions', $result);
        $this->assertEquals('PageSpecificFieldDefinitions', $result);
    }

    /**
     * @test
     */
    public function rollsbackInCaseOfError()
    {
        $rollbackTask = $this->createMock(RollbackTaskInterface::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);
        $propertyMappingConfiguration = $this->createMock(PropertyMappingConfiguration::class);
        $validationResult = $this->createMock(Result::class);

        $formState = $this->createMock(FormStateInterface::class);
        $formState->method('getValidationResult')->willReturn($validationResult);

        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition1->method('getName')->willReturn('Field1');
        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2->method('getName')->willReturn('Field2');
        $fieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition3->method('getName')->willReturn('Field3');

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'rollbackTask', $rollbackTask);
        $this->inject($formRuntime, 'propertyMappingConfiguration', $propertyMappingConfiguration);
        $this->inject($formRuntime, 'formState', $formState);

        $formState->expects($this->exactly(3))
            ->method('getArgument')
            ->withConsecutive(['Field1'], ['Field2'], ['Field3'])
            ->will($this->onConsecutiveCalls('Input1', 'Input2', null));
        $formState->expects($this->exactly(3))
            ->method('getValue')
            ->withConsecutive(['Field1'], ['Field2'], ['Field3'])
            ->will($this->onConsecutiveCalls(null, 'Value2', 'Value3'));
        $formState->expects($this->exactly(3))
            ->method('addValue')
            ->withConsecutive(
                ['Field1', 'RestoredValue1'],
                ['Field2', 'RestoredValue2'],
                ['Field3', 'RestoredValue3']
            );

        $formDefinition->method('hasPages')->willReturn(false);
        $formDefinition->method('getFieldDefinitions')
            ->willReturn([$fieldDefinition1, $fieldDefinition2, $fieldDefinition3]);

        //
        // Expect that the rollback task will be run three times
        //
        $rollbackTask->expects($this->exactly(3))
            ->method('run')
            ->withConsecutive(
                [$propertyMappingConfiguration, $this->identicalTo($fieldDefinition1), 'Input1', null, $validationResult],
                [$propertyMappingConfiguration, $this->identicalTo($fieldDefinition2), 'Input2', 'Value2', $validationResult],
                [$propertyMappingConfiguration, $this->identicalTo($fieldDefinition3), null, 'Value3', $validationResult]
            )
            ->will($this->onConsecutiveCalls('RestoredValue1', 'RestoredValue2', 'RestoredValue3'));;

        $formRuntime->rollback();
    }

    /**
     * @test
     */
    public function finishesForm()
    {
        $finishTask = $this->createMock(FinishTaskInterface::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);
        $response = $this->createMock(Response::class);

        $finisherDefinitions = [
            $this->createMock(FinisherDefinitionInterface::class),
            $this->createMock(FinisherDefinitionInterface::class)
        ];

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'finishTask', $finishTask);

        $formDefinition->expects($this->once())
            ->method('getFinisherDefinitions')
            ->willReturn($finisherDefinitions);

        //
        // Expect that the finish task will run exactly once
        //
        $finishTask->expects($this->once())
            ->method('run')
            ->with($this->identicalTo($finisherDefinitions), $this->identicalTo($response))
            ->willReturn('TheFinisherRuntime');

        $this->assertEquals('TheFinisherRuntime', $formRuntime->finish($response));
    }

    /**
     * @test
     */
    public function restoresFormStateOnInitialization()
    {
        $class = new \ReflectionClass(FormRuntime::class);
        $initializeObject = $class->getMethod('initializeObject');
        $initializeObject->setAccessible(true);

        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);
        $request->method('getArguments')->willReturn([]);
        $formStateFactory = $this->createMock(FormStateFactory::class);
        $propertyMappingConfigurationFactory = $this->createMock(PropertyMappingConfigurationFactory::class);
        $formState = $this->createMock(FormStateInterface::class);
        $formState->method('getArguments')->willReturn([]);

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'formStateFactory', $formStateFactory);
        $this->inject($formRuntime, 'propertyMappingConfigurationFactory', $propertyMappingConfigurationFactory);

        //
        // Expect that the form state factory will be called exactly once
        //
        $formStateFactory->expects($this->once())
            ->method('createFromActionRequest')
            ->with($formRuntime->getRequest())
            ->willReturn($formState);

        $initializeObject->invoke($formRuntime);
    }

    /**
     * @test
     */
    public function mergesRequestArgumentsWithPersistedFormStateArgumentsOnInitialization()
    {
        $class = new \ReflectionClass(FormRuntime::class);
        $initializeObject = $class->getMethod('initializeObject');
        $initializeObject->setAccessible(true);

        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);
        $propertyMappingConfigurationFactory = $this->createMock(PropertyMappingConfigurationFactory::class);
        $formState = $this->createMock(FormStateInterface::class);
        $formStateFactory = $this->createMock(FormStateFactory::class);
        $formStateFactory->method('createFromActionRequest')->willReturn($formState);

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'formStateFactory', $formStateFactory);
        $this->inject($formRuntime, 'propertyMappingConfigurationFactory', $propertyMappingConfigurationFactory);
        $this->inject($formRuntime, 'request', $request);

        //
        // Expect that request arguments and form state arguments will be merged
        //
        $formState->expects($this->once())
            ->method('mergeArguments')
            ->with($this->equalTo([
                'Test2' => 'OverriddenValue',
                'Test3' => 'AddedValue'
            ]))
            ->willReturn([
                'Test1' => 'Value1',
                'Test2' => 'Value2',
                'Test4' => 'Value4'
            ]);
        $request->expects($this->once())
            ->method('getArguments')
            ->willReturn([
                'Test2' => 'OverriddenValue',
                'Test3' => 'AddedValue'
            ]);

        $initializeObject->invoke($formRuntime);
    }

    /**
     * @test
     */
    public function createsPropertyMappingConfigurationOnInitialization()
    {
        $class = new \ReflectionClass(FormRuntime::class);
        $initializeObject = $class->getMethod('initializeObject');
        $initializeObject->setAccessible(true);
        $propertyMappingConfiguration = $class->getProperty('propertyMappingConfiguration');
        $propertyMappingConfiguration->setAccessible(true);

        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);
        $request->method('getArguments')->willReturn([]);
        $propertyMappingConfigurationFactory = $this->createMock(PropertyMappingConfigurationFactory::class);
        $formState = $this->createMock(FormStateInterface::class);
        $formState->method('getArguments')->willReturn([]);
        $formStateFactory = $this->createMock(FormStateFactory::class);
        $formStateFactory->method('createFromActionRequest')->willReturn($formState);

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'formStateFactory', $formStateFactory);
        $this->inject($formRuntime, 'propertyMappingConfigurationFactory', $propertyMappingConfigurationFactory);
        $this->inject($formRuntime, 'request', $request);

        //
        // Expect that the property mapping configuration factory will be called exactly once
        //
        $request->expects($this->once())
            ->method('getInternalArgument')
            ->with($this->equalTo('__trustedProperties'))
            ->willReturn('TheTrustedPropertiesToken');

        $propertyMappingConfigurationFactory->expects($this->once())
            ->method('createTrustedPropertyMappingConfiguration')
            ->with('TheTrustedPropertiesToken')
            ->willReturn('TheTrustedPropertymappingConfiguration');

        $initializeObject->invoke($formRuntime);

        $this->assertEquals('TheTrustedPropertymappingConfiguration',
            $propertyMappingConfiguration->getValue($formRuntime));
    }

    /**
     * @test
     */
    public function checksIfFormOrPageShouldBeProcessed()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);

        $formState1 = $this->createMock(FormStateInterface::class);
        $formState1->method('isInitialCall')->willReturn(true);
        $formState2 = $this->createMock(FormStateInterface::class);
        $formState2->method('isInitialCall')->willReturn(false);

        $formRuntime = new FormRuntime($formDefinition, $request);

        $this->inject($formRuntime, 'formState', $formState1);

        $this->assertFalse($formRuntime->shouldProcess());

        $this->inject($formRuntime, 'formState', $formState2);

        $this->assertTrue($formRuntime->shouldProcess());
    }

    /**
     * @test
     */
    public function checksIfFormOrPageShouldBeValidated()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);

        $formState1 = $this->createMock(FormStateInterface::class);
        $formState1->method('isInitialCall')->willReturn(true);
        $formState2 = $this->createMock(FormStateInterface::class);
        $formState2->method('isInitialCall')->willReturn(false);
        $formState2->method('getValues')->willReturn(null);
        $formState3 = $this->createMock(FormStateInterface::class);
        $formState3->method('isInitialCall')->willReturn(false);
        $formState3->method('getValues')->willReturn([]);
        $formState4 = $this->createMock(FormStateInterface::class);
        $formState4->method('isInitialCall')->willReturn(false);
        $formState4->method('getValues')->willReturn(['SomeValue']);

        $formRuntime = new FormRuntime($formDefinition, $request);

        $this->inject($formRuntime, 'formState', $formState1);

        $this->assertFalse($formRuntime->shouldValidate());

        $this->inject($formRuntime, 'formState', $formState2);

        $this->assertFalse($formRuntime->shouldValidate());

        $this->inject($formRuntime, 'formState', $formState3);

        $this->assertFalse($formRuntime->shouldValidate());

        $this->inject($formRuntime, 'formState', $formState4);

        $this->assertTrue($formRuntime->shouldValidate());
    }

    /**
     * @test
     */
    public function checksIfProcessorsShouldBeRolledBack()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);

        $validationResult1 = $this->createMock(Result::class);
        $validationResult1->method('hasErrors')->willReturn(false);
        $validationResult2 = $this->createMock(Result::class);
        $validationResult2->method('hasErrors')->willReturn(true);

        $formState1 = $this->createMock(FormStateInterface::class);
        $formState1->method('getValidationResult')->willReturn($validationResult1);
        $formState2 = $this->createMock(FormStateInterface::class);
        $formState2->method('getValidationResult')->willReturn($validationResult2);

        $formRuntime = new FormRuntime($formDefinition, $request);

        $this->inject($formRuntime, 'formState', $formState1);

        $this->assertFalse($formRuntime->shouldRollback());

        $this->inject($formRuntime, 'formState', $formState2);

        $this->assertTrue($formRuntime->shouldRollback());
    }

    public function formStateProvider()
    {
        $validationResultWithErrors = $this->createMock(Result::class);
        $validationResultWithErrors->method('hasErrors')->willReturn(true);
        $validationResultWithoutErrors = $this->createMock(Result::class);
        $validationResultWithoutErrors->method('hasErrors')->willReturn(false);

        $formStateMock = function (FormStateInterface $mock, $isInitial = true, $hasErrors = false, $pageName = '')
            use ($validationResultWithErrors, $validationResultWithoutErrors) {
            $mock->method('isInitialCall')->willReturn($isInitial);
            $mock->method('getCurrentPage')->willReturn($pageName);
            $mock->method('getValidationResult')->willReturn(
                $hasErrors ? $validationResultWithErrors : $validationResultWithoutErrors
            );

            return $mock;
        };

        return [
            [
                'FormState: Is initial call, has no validation errors, has no pages',
                $formStateMock($this->createMock(FormStateInterface::class)),
                false,
                false
            ],
            [
                'FormState: Is subsequent call, has no validation errors, has no pages',
                $formStateMock($this->createMock(FormStateInterface::class), false),
                false,
                true
            ],
            [
                'FormState: Is initial call, has validation errors, has no pages',
                $formStateMock($this->createMock(FormStateInterface::class), true, true),
                false,
                false
            ],
            [
                'FormState: Is subsequent call, has validation errors, has no pages',
                $formStateMock($this->createMock(FormStateInterface::class), false, true),
                false,
                false
            ],
            [
                'FormState: Is initial call, has no validation errors, has pages, is on first page',
                $formStateMock($this->createMock(FormStateInterface::class), true, false, 'PageDefinition1'),
                true,
                false
            ],
            [
                'FormState: Is subsequent call, has no validation errors, has pages, is on first page',
                $formStateMock($this->createMock(FormStateInterface::class), false, false, 'PageDefinition1'),
                true,
                false
            ],
            [
                'FormState: Is initial call, has validation errors, has pages, is on first page',
                $formStateMock($this->createMock(FormStateInterface::class), true, true, 'PageDefinition1'),
                true,
                false
            ],
            [
                'FormState: Is subsequent call, has validation errors, has pages, is on first page',
                $formStateMock($this->createMock(FormStateInterface::class), false, true, 'PageDefinition1'),
                true,
                false
            ],
            [
                'FormState: Is initial call, has no validation errors, has pages, is on middle page',
                $formStateMock($this->createMock(FormStateInterface::class), true, false, 'PageDefinition2'),
                true,
                false
            ],
            [
                'FormState: Is subsequent call, has no validation errors, has pages, is on middle page',
                $formStateMock($this->createMock(FormStateInterface::class), false, false, 'PageDefinition2'),
                true,
                false
            ],
            [
                'FormState: Is initial call, has validation errors, has pages, is on middle page',
                $formStateMock($this->createMock(FormStateInterface::class), true, true, 'PageDefinition2'),
                true,
                false
            ],
            [
                'FormState: Is subsequent call, has validation errors, has pages, is on middle page',
                $formStateMock($this->createMock(FormStateInterface::class), false, true, 'PageDefinition2'),
                true,
                false
            ],
            [
                'FormState: Is initial call, has no validation errors, has pages, is on last page',
                $formStateMock($this->createMock(FormStateInterface::class), true, false, 'PageDefinition3'),
                true,
                false
            ],
            [
                'FormState: Is subsequent call, has no validation errors, has pages, is on last page',
                $formStateMock($this->createMock(FormStateInterface::class), false, false, 'PageDefinition3'),
                true,
                true
            ],
            [
                'FormState: Is initial call, has validation errors, has pages, is on last page',
                $formStateMock($this->createMock(FormStateInterface::class), true, true, 'PageDefinition3'),
                true,
                false
            ],
            [
                'FormState: Is subsequent call, has validation errors, has pages, is on last page',
                $formStateMock($this->createMock(FormStateInterface::class), false, true, 'PageDefinition3'),
                true,
                false
            ]
        ];
    }

    /**
     * @test
     * @dataProvider formStateProvider
     */
    public function checksIfMultiPageFormShouldBeFinished($description, $formState, $hasPages, $shouldFinish)
    {
        $request = $this->createMock(ActionRequest::class);

        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $formDefinition->method('hasPages')->willReturn($hasPages);

        if ($hasPages) {
            $pageDefinition1 = $this->createMock(PageDefinitionInterface::class);
            $pageDefinition1->method('getName')->willReturn('PageDefinition1');
            $pageDefinition2 = $this->createMock(PageDefinitionInterface::class);
            $pageDefinition2->method('getName')->willReturn('PageDefinition2');
            $pageDefinition3 = $this->createMock(PageDefinitionInterface::class);
            $pageDefinition3->method('getName')->willReturn('PageDefinition3');

            $formDefinition->method('getPageDefinitions')->willReturn([
                $pageDefinition1,
                $pageDefinition2,
                $pageDefinition3
            ]);
        }

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'formState', $formState);

        $this->assertSame($shouldFinish, $formRuntime->shouldFinish(), $description);
    }
}
