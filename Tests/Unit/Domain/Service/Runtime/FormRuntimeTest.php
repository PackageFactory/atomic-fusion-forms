<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Mvc\ActionRequest;
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Http\Response;
use TYPO3\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Factory\FormStateFactory;
use PackageFactory\AtomicFusion\Forms\Factory\PropertyMappingConfigurationFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormStateInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Tasks\ProcessTaskInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Tasks\ValidateTaskInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Tasks\RollbackTaskInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Tasks\FinishTaskInterface;

interface __getterStub__1476736746 {
	public function getTest1();
	public function getTest2();
}

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

        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition1->method('getName')->willReturn('Field1');
        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2->method('getName')->willReturn('Field2');
        $fieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition3->method('getName')->willReturn('Field3');

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'processTask', $processTask);
        $this->inject($formRuntime, 'propertyMappingConfiguration', $propertyMappingConfiguration);
        $this->inject($formRuntime, 'validationResult', $validationResult);
        $this->inject($formRuntime, 'arguments', [
            'Field1' => 'Input1',
            'Field2' => 'Input2'
        ]);

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
            );

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

        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition1->method('getName')->willReturn('Field1');
        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2->method('getName')->willReturn('Field2');
        $fieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition3->method('getName')->willReturn('Field3');

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'validateTask', $validateTask);
        $this->inject($formRuntime, 'validationResult', $validationResult);
        $this->inject($formRuntime, 'values', [
            'Field1' => 'Value1',
            'Field2' => 'Value2'
        ]);

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

        $fieldDefinition1 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition1->method('getName')->willReturn('Field1');
        $fieldDefinition2 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition2->method('getName')->willReturn('Field2');
        $fieldDefinition3 = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition3->method('getName')->willReturn('Field3');

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'rollbackTask', $rollbackTask);
        $this->inject($formRuntime, 'propertyMappingConfiguration', $propertyMappingConfiguration);
        $this->inject($formRuntime, 'validationResult', $validationResult);
        $this->inject($formRuntime, 'arguments', [
            'Field1' => 'Input1',
            'Field2' => 'Input2'
        ]);
        $this->inject($formRuntime, 'values', [
            'Field2' => 'Value2',
            'Field3' => 'Value3'
        ]);

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
            );

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
        $arguments = $class->getProperty('arguments');
        $arguments->setAccessible(true);

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
            ->method('getArguments')
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

        $this->assertEquals([
            'Test1' => 'Value1',
            'Test2' => 'OverriddenValue',
            'Test3' => 'AddedValue',
            'Test4' => 'Value4'
        ], $arguments->getValue($formRuntime));
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

        $formRuntime = new FormRuntime($formDefinition, $request);

        $this->inject($formRuntime, 'formState', $formState1);

        $this->assertFalse($formRuntime->shouldValidate());

        $this->inject($formRuntime, 'formState', $formState2);
        $this->inject($formRuntime, 'values', null);

        $this->assertFalse($formRuntime->shouldValidate());

        $this->inject($formRuntime, 'values', []);

        $this->assertFalse($formRuntime->shouldValidate());

        $this->inject($formRuntime, 'values', ['SomeValue']);

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

        $formRuntime = new FormRuntime($formDefinition, $request);

        $this->inject($formRuntime, 'validationResult', $validationResult1);

        $this->assertFalse($formRuntime->shouldRollback());

        $this->inject($formRuntime, 'validationResult', $validationResult2);

        $this->assertTrue($formRuntime->shouldRollback());
    }

    /**
     * @test
     */
    public function checksIfMultiPageFormShouldBeFinished()
    {
        $formDefinition1 = $this->createMock(FormDefinitionInterface::class);
        $formDefinition2 = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);

        $pageDefinition1 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition1->method('getName')->willReturn('PageDefinition1');
        $pageDefinition2 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition2->method('getName')->willReturn('PageDefinition2');
        $pageDefinition3 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition3->method('getName')->willReturn('PageDefinition3');

        $formDefinition1->method('hasPages')->willReturn(false);
        $formDefinition2->method('hasPages')->willReturn(true);
        $formDefinition2->method('getPageDefinitions')->willReturn([
            $pageDefinition1,
            $pageDefinition2,
            $pageDefinition3
        ]);

        $formStateI = $this->createMock(FormStateInterface::class);
        $formStateI->method('isInitialCall')->willReturn(true);
        $formStateS = $this->createMock(FormStateInterface::class);
        $formStateS->method('isInitialCall')->willReturn(false);
        $formState1I = $this->createMock(FormStateInterface::class);
        $formState1I->method('isInitialCall')->willReturn(true);
        $formState1I->method('getCurrentPage')->willReturn('PageDefinition1');
        $formState1S = $this->createMock(FormStateInterface::class);
        $formState1S->method('isInitialCall')->willReturn(false);
        $formState1S->method('getCurrentPage')->willReturn('PageDefinition1');
        $formState2I = $this->createMock(FormStateInterface::class);
        $formState2I->method('isInitialCall')->willReturn(true);
        $formState2I->method('getCurrentPage')->willReturn('PageDefinition2');
        $formState2S = $this->createMock(FormStateInterface::class);
        $formState2S->method('isInitialCall')->willReturn(false);
        $formState2S->method('getCurrentPage')->willReturn('PageDefinition2');
        $formState3I = $this->createMock(FormStateInterface::class);
        $formState3I->method('isInitialCall')->willReturn(true);
        $formState3I->method('getCurrentPage')->willReturn('PageDefinition3');
        $formState3S = $this->createMock(FormStateInterface::class);
        $formState3S->method('isInitialCall')->willReturn(false);
        $formState3S->method('getCurrentPage')->willReturn('PageDefinition3');

        $validationResult1 = $this->createMock(Result::class);
        $validationResult1->method('hasErrors')->willReturn(false);
        $validationResult2 = $this->createMock(Result::class);
        $validationResult2->method('hasErrors')->willReturn(true);

        $formRuntime1 = new FormRuntime($formDefinition1, $request);
        $formRuntime2 = new FormRuntime($formDefinition2, $request);

        //
        // Has no pages, is initial call, validation result has no errors
        //
        $this->inject($formRuntime1, 'validationResult', $validationResult1);
        $this->inject($formRuntime1, 'formState', $formStateI);

        $this->assertFalse(
            $formRuntime1->shouldFinish(),
            'Has no pages, is initial call, validation result has no errors'
        );

        //
        // Has no pages, is initial call, validation result has errors
        //
        $this->inject($formRuntime1, 'validationResult', $validationResult2);
        $this->inject($formRuntime1, 'formState', $formStateI);

        $this->assertFalse(
            $formRuntime1->shouldFinish(),
            'Has no pages, is initial call, validation result has errors'
        );

        //
        // Has pages, is on first page, is initial call, validation result has no errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult1);
        $this->inject($formRuntime2, 'formState', $formState1I);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on first page, is initial call, validation result has no errors'
        );

        //
        // Has pages, is on middle page, is initial call, validation result has no errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult1);
        $this->inject($formRuntime2, 'formState', $formState2I);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on middle page, is initial call, validation result has no errors'
        );

        //
        // Has pages, is on last page, is initial call, validation result has no errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult1);
        $this->inject($formRuntime2, 'formState', $formState3I);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on last page, is initial call, validation result has no errors'
        );

        //
        // Has pages, is on first page, is initial call, validation result has errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult2);
        $this->inject($formRuntime2, 'formState', $formState1I);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on first page, is initial call, validation result has errors'
        );

        //
        // Has pages, is on middle page, is initial call, validation result has errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult2);
        $this->inject($formRuntime2, 'formState', $formState2I);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on middle page, is initial call, validation result has errors'
        );

        //
        // Has pages, is on last page, is initial call, validation result has errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult2);
        $this->inject($formRuntime2, 'formState', $formState3I);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on last page, is initial call, validation result has errors'
        );

        //
        // Has no pages, is subsequent call, validation result has no errors
        //
        $this->inject($formRuntime1, 'validationResult', $validationResult1);
        $this->inject($formRuntime1, 'formState', $formStateS);

        $this->assertTrue(
            $formRuntime1->shouldFinish(),
            'Has no pages, is subsequent call, validation result has no errors'
        );

        //
        // Has no pages, is subsequent call, validation result has errors
        //
        $this->inject($formRuntime1, 'validationResult', $validationResult2);
        $this->inject($formRuntime1, 'formState', $formStateS);

        $this->assertFalse(
            $formRuntime1->shouldFinish(),
            'Has no pages, is subsequent call, validation result has errors'
        );

        //
        // Has pages, is on first page, is subsequent call, validation result has no errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult1);
        $this->inject($formRuntime2, 'formState', $formState1S);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on first page, is subsequent call, validation result has no errors'
        );

        //
        // Has pages, is on middle page, is subsequent call, validation result has no errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult1);
        $this->inject($formRuntime2, 'formState', $formState2S);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on middle page, is subsequent call, validation result has no errors'
        );

        //
        // Has pages, is on last page, is subsequent call, validation result has no errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult1);
        $this->inject($formRuntime2, 'formState', $formState3S);

        $this->assertTrue(
            $formRuntime2->shouldFinish(),
            'Has pages, is on last page, is subsequent call, validation result has no errors'
        );

        //
        // Has pages, is on first page, is subsequent call, validation result has errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult2);
        $this->inject($formRuntime2, 'formState', $formState1S);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on first page, is subsequent call, validation result has errors'
        );

        //
        // Has pages, is on middle page, is subsequent call, validation result has errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult2);
        $this->inject($formRuntime2, 'formState', $formState2S);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on middle page, is subsequent call, validation result has errors'
        );

        //
        // Has pages, is on last page, is subsequent call, validation result has errors
        //
        $this->inject($formRuntime2, 'validationResult', $validationResult2);
        $this->inject($formRuntime2, 'formState', $formState3S);

        $this->assertFalse(
            $formRuntime2->shouldFinish(),
            'Has pages, is on last page, is subsequent call, validation result has errors'
        );
    }

    /**
     * @test
     */
    public function deliversArgumentsByPath()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);

        $formRuntime = new FormRuntime($formDefinition, $request);
        $this->inject($formRuntime, 'arguments', [
            'toplevel' => 'Test1',
            'pretty' => [
                'deeply' => [
                    'nested' => 'Test2'
                ]
            ]
        ]);

        $this->assertEquals('Test1', $formRuntime->getArgument('toplevel'));
        $this->assertEquals(['deeply' => [
            'nested' => 'Test2'
        ]], $formRuntime->getArgument('pretty'));
        $this->assertEquals(['nested' => 'Test2'], $formRuntime->getArgument('pretty.deeply'));
        $this->assertEquals('Test2', $formRuntime->getArgument('pretty.deeply.nested'));
    }

    /**
     * @test
     */
    public function deliversValuesByPath()
    {
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $request = $this->createMock(ActionRequest::class);

        $formRuntime = new FormRuntime($formDefinition, $request);

        $stub1 = $this->createMock(__getterStub__1476736746::class);
        $stub2 = $this->createMock(__getterStub__1476736746::class);

        $stub1->method('getTest1')->willReturn($stub2);
        $stub2->method('getTest2')->willReturn('Test3');

        $this->inject($formRuntime, 'values', [
            'toplevel' => 'Test1',
            'pretty' => [
                'deeply' => [
                    'nested' => 'Test2'
                ]
            ],
            'nested' => $stub1
        ]);

        $this->assertEquals('Test1', $formRuntime->getValue('toplevel'));
        $this->assertEquals(['deeply' => [
            'nested' => 'Test2'
        ]], $formRuntime->getValue('pretty'));
        $this->assertEquals(['nested' => 'Test2'], $formRuntime->getValue('pretty.deeply'));
        $this->assertEquals('Test2', $formRuntime->getValue('pretty.deeply.nested'));
        $this->assertEquals($stub1, $formRuntime->getValue('nested'));
        $this->assertEquals($stub2, $formRuntime->getValue('nested.test1'));
        $this->assertEquals('Test3', $formRuntime->getValue('nested.test1.test2'));
    }
}
