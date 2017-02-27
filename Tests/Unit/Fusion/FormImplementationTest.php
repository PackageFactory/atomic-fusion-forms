<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Flow\Mvc\Controller\ControllerContext;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Mvc\FlashMessageContainer;
use Neos\Error\Messages\Result;
use Neos\Error\Messages\Message;
use Neos\Flow\Http\Response;
use Neos\Fusion\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Factory\FormRuntimeFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormStateInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FinisherStateInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\FormDefinitionFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Context\FormContext;
use PackageFactory\AtomicFusion\Forms\Domain\Context\PageContext;
use PackageFactory\AtomicFusion\Forms\Domain\Context\Factory\FormContextFactory;
use PackageFactory\AtomicFusion\Forms\Fusion\FormImplementation;

class FormImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function deliversFormDefinition()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formDefinitionFactory = $this->createMock(FormDefinitionFactory::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');

        $this->inject($formImplementation, 'formDefinitionFactory', $formDefinitionFactory);

        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $fieldDefinition = $this->createMock(FieldDefinitionInterface::class);
        $fieldDefinition->method('getFormDefinition')->willReturn($formDefinition);
        $finisherDefinition = $this->createMock(FinisherDefinitionInterface::class);
        $pageDefinition = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition->method('getFormDefinition')->willReturn($formDefinition);

        $formDefinitionFactory->expects($this->once())
            ->method('createFormDefinition')
            ->with([
                'label' => 'SomeLabel',
    			'name' => 'SomeName',
    			'action' => 'SomeAction'
            ])
            ->willReturn($formDefinition);

        $fusionRuntime->expects($this->exactly(6))
            ->method('evaluate')
            ->withConsecutive(
                ['/fields', $formImplementation],
                ['/finishers', $formImplementation],
                ['/pages', $formImplementation],
                ['/label', $formImplementation],
                ['/name', $formImplementation],
                ['/action', $formImplementation]
            )
            ->will($this->onConsecutiveCalls(
                [$fieldDefinition],
                [$finisherDefinition],
                [$pageDefinition],
                'SomeLabel',
                'SomeName',
                'SomeAction'
            ));

        $formDefinition->expects($this->once())
            ->method('addFieldDefinition')
            ->with($this->identicalTo($fieldDefinition));
        $formDefinition->expects($this->once())
            ->method('addFinisherDefinition')
            ->with($this->identicalTo($finisherDefinition));
        $formDefinition->expects($this->once())
            ->method('addPageDefinition')
            ->with($this->identicalTo($pageDefinition));

        //
        // Perform twice, call count should stay the same
        //
        $result = $formImplementation->getFormDefinition();
        $result = $formImplementation->getFormDefinition();

        $this->assertSame($formDefinition, $formDefinition);
    }

    /**
     * @test
     */
    public function deliversFormRuntime()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $formRuntimeFactory = $this->createMock(FormRuntimeFactory::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $controllerContext = $this->createMock(ControllerContext::class);
        $actionRequest = $this->createMock(ActionRequest::class);
        $formImplementation = $this->getAccessibleMock(
            FormImplementation::class,
            ['getFormDefinition'],
            [$fusionRuntime, '', '']
        );

        $this->inject($formImplementation, 'formRuntimeFactory', $formRuntimeFactory);

        $formImplementation->expects($this->once())
            ->method('getFormDefinition')
            ->willReturn($formDefinition);

        $fusionRuntime->expects($this->once())->method('getControllerContext')->willReturn($controllerContext);
        $controllerContext->expects($this->once())->method('getRequest')->willReturn($actionRequest);

        $formRuntimeFactory->expects($this->once())
            ->method('createFormRuntime')
            ->with($this->identicalTo($formDefinition), $this->identicalTo($actionRequest))
            ->willReturn($formRuntime);

        //
        // Check twice, call count should stay the same
        //
        $this->assertEquals($formRuntime, $formImplementation->getFormRuntime());
        $this->assertEquals($formRuntime, $formImplementation->getFormRuntime());
    }

    /**
     * @test
     */
    public function processesAndRendersAndAugmentsTheForm()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formContextFactory = $this->createMock(FormContextFactory::class);
        $formContext = $this->createMock(FormContext::class);
        $formImplementation = $this->getAccessibleMock(
            FormImplementation::class,
            ['getFormRuntime', 'processForm', 'renderForm', 'augmentForm'],
            [$fusionRuntime, '', '']
        );

        $this->inject($formImplementation, 'formContextFactory', $formContextFactory);

        $formImplementation->expects($this->once())
            ->method('getFormRuntime')
            ->willReturn($formRuntime);

        $formContextFactory->expects($this->once())
            ->method('createFormContext')
            ->with($this->identicalTo($formRuntime))
            ->willReturn($formContext);

        $fusionRuntime->method('getCurrentContext')->willReturn([]);

        $fusionRuntime->expects($this->once())
            ->method('evaluate')
            ->with('/formContext', $formImplementation)
            ->willReturn('form');

        $fusionRuntime->expects($this->once())
            ->method('pushContextArray')
            ->with($this->equalTo(['form' => $formContext]));

        $fusionRuntime->expects($this->once())
            ->method('popContext');

        $formImplementation->expects($this->once())
            ->method('processForm')
            ->with($this->identicalTo($formRuntime))
            ->willReturn(null);

        $formImplementation->expects($this->once())
            ->method('renderForm')
            ->with($this->identicalTo($formRuntime), $this->identicalTo($formContext))
            ->willReturn('TheRenderedForm');

        $formImplementation->expects($this->once())
            ->method('augmentForm')
            ->with(
                $this->equalTo('TheRenderedForm'),
                $this->identicalTo($formRuntime),
                $this->identicalTo($formContext)
            )
            ->willReturn('TheAugmentedForm');

        $this->assertEquals('TheAugmentedForm', $formImplementation->evaluate());
    }

    /**
     * @test
     */
    public function skipsRenderingAndAugmentingTheFormIfProcessingReturnedStringResult()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formContextFactory = $this->createMock(FormContextFactory::class);
        $formContext = $this->createMock(FormContext::class);
        $formImplementation = $this->getAccessibleMock(
            FormImplementation::class,
            ['getFormRuntime', 'processForm', 'renderForm', 'augmentForm'],
            [$fusionRuntime, '', '']
        );

        $this->inject($formImplementation, 'formContextFactory', $formContextFactory);

        $formImplementation->method('getFormRuntime')->willReturn($formRuntime);
        $formContextFactory->method('createFormContext')->willReturn($formContext);
        $fusionRuntime->method('getCurrentContext')->willReturn([]);

        $formImplementation->expects($this->once())
            ->method('processForm')
            ->with($this->identicalTo($formRuntime))
            ->willReturn('TheProcessedForm');

        $formImplementation->expects($this->never())
            ->method('renderForm')
            ->willReturn('TheRenderedForm');

        $formImplementation->expects($this->never())
            ->method('augmentForm')
            ->willReturn('TheAugmentedForm');

        $this->assertEquals('TheProcessedForm', $formImplementation->evaluate());
    }

    /**
     * @test
     */
    public function rendersRendererIfFormIsNotPaged()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formContext = $this->createMock(FormContext::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');

        $formRuntime->expects($this->once())
            ->method('getFormDefinition')
            ->willReturn($formDefinition);

        $formDefinition->expects($this->once())
            ->method('hasPages')
            ->willReturn(false);

        $fusionRuntime->expects($this->once())
            ->method('render')
            ->with($this->equalTo('/renderer'))
            ->willReturn('TheRenderedForm');

        $this->assertEquals('TheRenderedForm', $formImplementation->renderForm($formRuntime, $formContext));
    }

    /**
     * @test
     */
    public function rendersFirstPageIfFormIsPaged()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formContext = $this->createMock(FormContext::class);
        $pageContext = $this->createMock(PageContext::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $validationResult = $this->createMock(Result::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');

        $formRuntime->expects($this->once())
            ->method('getFormDefinition')
            ->willReturn($formDefinition);

        $formRuntime->expects($this->once())
            ->method('getFormState')
            ->willReturn($formState);

        $formDefinition->expects($this->once())
            ->method('hasPages')
            ->willReturn(true);

        $formDefinition->expects($this->once())
            ->method('getNextPage')
            ->with('NullPage')
            ->willReturn('TheCurrentPage');

        $formState->expects($this->once())
            ->method('getValidationResult')
            ->willReturn($validationResult);

        $formState->expects($this->once())
            ->method('getCurrentPage')
            ->willReturn('NullPage');

        $validationResult->expects($this->once())
            ->method('hasErrors')
            ->willReturn(false);

        $formContext->expects($this->once())
            ->method('page')
            ->with('TheCurrentPage')
            ->willReturn($pageContext);

        $fusionRuntime->expects($this->once())
            ->method('evaluate')
            ->with('/pageContext', $formImplementation)
            ->willReturn('page');

        $fusionRuntime->method('getCurrentContext')->willReturn([]);

        $fusionRuntime->expects($this->once())
            ->method('pushContextArray')
            ->with($this->equalTo(['page' => $pageContext]));

        $fusionRuntime->expects($this->once())
            ->method('popContext');

        $fusionRuntime->expects($this->once())
            ->method('render')
            ->with($this->equalTo('/renderer/TheCurrentPage'))
            ->willReturn('TheRenderedForm');

        $this->assertEquals('TheRenderedForm', $formImplementation->renderForm($formRuntime, $formContext));
    }

    /**
     * @test
     */
    public function staysOnPageIfFormIsPagedAndHasErrors()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formContext = $this->createMock(FormContext::class);
        $pageContext = $this->createMock(PageContext::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $validationResult = $this->createMock(Result::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');

        $formRuntime->method('getFormDefinition')->willReturn($formDefinition);
        $formRuntime->method('getFormState')->willReturn($formState);
        $formDefinition->method('hasPages')->willReturn(true);
        $formState->method('getValidationResult')->willReturn($validationResult);
        $validationResult->method('hasErrors')->willReturn(true);
        $formState->method('getCurrentPage')->willReturn('TheCurrentPage');
        $fusionRuntime->method('getCurrentContext')->willReturn([]);

        $formDefinition->expects($this->never())
            ->method('getNextPage')
            ->WillReturn('TheNextPage');

        $formContext->expects($this->once())
            ->method('page')
            ->with('TheCurrentPage')
            ->willReturn($pageContext);

        $fusionRuntime->expects($this->once())
            ->method('render')
            ->with($this->equalTo('/renderer/TheCurrentPage'))
            ->willReturn('TheRenderedForm');

        $this->assertEquals('TheRenderedForm', $formImplementation->renderForm($formRuntime, $formContext));
    }

    /**
     * @test
     */
    public function processesFormIfProcessingIsNeeded()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');

        $formRuntime->expects($this->once())
            ->method('shouldProcess')
            ->willReturn(true);

        $formRuntime->expects($this->once())
            ->method('process');

        $formRuntime->expects($this->never())
            ->method('validate');

        $formRuntime->expects($this->never())
            ->method('rollback');

        $formRuntime->expects($this->never())
            ->method('finish');

        $formImplementation->processForm($formRuntime);
    }

    /**
     * @test
     */
    public function validatesFormIfValidationIsNeeded()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');

        $formRuntime->expects($this->once())
            ->method('shouldProcess')
            ->willReturn(true);

        $formRuntime->expects($this->once())
            ->method('shouldValidate')
            ->willReturn(true);

        $formRuntime->expects($this->once())
            ->method('process');

        $formRuntime->expects($this->once())
            ->method('validate');

        $formRuntime->expects($this->never())
            ->method('rollback');

        $formRuntime->expects($this->never())
            ->method('finish');

        $formImplementation->processForm($formRuntime);
    }

    /**
     * @test
     */
    public function rollsBackFormIfRollbackIsNeeded()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');

        $formRuntime->expects($this->once())
            ->method('shouldProcess')
            ->willReturn(true);

        $formRuntime->expects($this->once())
            ->method('shouldValidate')
            ->willReturn(true);

        $formRuntime->expects($this->once())
            ->method('shouldRollback')
            ->willReturn(true);

        $formRuntime->expects($this->once())
            ->method('process');

        $formRuntime->expects($this->once())
            ->method('validate');

        $formRuntime->expects($this->once())
            ->method('rollback');

        $formRuntime->expects($this->never())
            ->method('finish');

        $formImplementation->processForm($formRuntime);
    }

    /**
     * @test
     */
    public function finishesFormIfFinishingIsNeeded()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $finisherState = $this->createMock(FinisherStateInterface::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');
        $controllerContext = $this->createMock(ControllerContext::class);
        $originalResponse = $this->createMock(Response::class);
        $formStateResponse = $this->createMock(Response::class);
        $flashMessageContainer = $this->createMock(FlashMessageContainer::class);

        $formRuntime->expects($this->once())
            ->method('shouldProcess')
            ->willReturn(true);

        $formRuntime->expects($this->once())
            ->method('shouldValidate')
            ->willReturn(true);

        $formRuntime->expects($this->once())
            ->method('shouldRollback')
            ->willReturn(false);

        $formRuntime->expects($this->once())
            ->method('shouldFinish')
            ->willReturn(true);

        $formRuntime->expects($this->once())
            ->method('process');

        $formRuntime->expects($this->once())
            ->method('validate');

        $formRuntime->expects($this->never())
            ->method('rollback');

        $fusionRuntime->expects($this->once())
            ->method('getControllerContext')
            ->willReturn($controllerContext);

        $controllerContext->expects($this->once())
            ->method('getResponse')
            ->willReturn($originalResponse);

        $formRuntime->expects($this->once())
            ->method('finish')
            ->willReturn($finisherState);

        $finisherState->expects($this->once())
            ->method('getResponse')
            ->willReturn($formStateResponse);

        $finisherState->expects($this->once())
            ->method('getFlashMessageContainer')
            ->willReturn($flashMessageContainer);

        $formImplementation->processForm($formRuntime);
    }

    /**
     * @test
     */
    public function rendersFinisherStateContentIfFinishingIsNeeded()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $finisherState = $this->createMock(FinisherStateInterface::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');
        $controllerContext = $this->createMock(ControllerContext::class);
        $originalResponse = $this->createMock(Response::class);
        $formStateResponse = $this->createMock(Response::class);
        $flashMessageContainer = $this->createMock(FlashMessageContainer::class);

        $formRuntime->method('shouldProcess')->willReturn(true);
        $formRuntime->method('shouldValidate')->willReturn(true);
        $formRuntime->method('shouldRollback')->willReturn(false);
        $formRuntime->method('shouldFinish')->willReturn(true);
        $fusionRuntime->method('getControllerContext')->willReturn($controllerContext);
        $controllerContext->method('getResponse')->willReturn($originalResponse);
        $formRuntime->method('finish')->willReturn($finisherState);
        $finisherState->method('getResponse')->willReturn($formStateResponse);
        $finisherState->method('getFlashMessageContainer')->willReturn($flashMessageContainer);

        $formStateResponse->expects($this->once())
            ->method('getContent')
            ->willReturn('TheProcessingResult');

        $this->assertEquals('TheProcessingResult', $formImplementation->processForm($formRuntime));
    }

    /**
     * @test
     */
    public function setsHttpStatusCodeIfFinishingIsNeeded()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $finisherState = $this->createMock(FinisherStateInterface::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');
        $controllerContext = $this->createMock(ControllerContext::class);
        $originalResponse = $this->createMock(Response::class);
        $formStateResponse = $this->createMock(Response::class);
        $flashMessageContainer = $this->createMock(FlashMessageContainer::class);

        $formRuntime->method('shouldProcess')->willReturn(true);
        $formRuntime->method('shouldValidate')->willReturn(true);
        $formRuntime->method('shouldRollback')->willReturn(false);
        $formRuntime->method('shouldFinish')->willReturn(true);
        $fusionRuntime->method('getControllerContext')->willReturn($controllerContext);
        $controllerContext->method('getResponse')->willReturn($originalResponse);
        $formRuntime->method('finish')->willReturn($finisherState);
        $finisherState->method('getResponse')->willReturn($formStateResponse);
        $finisherState->method('getFlashMessageContainer')->willReturn($flashMessageContainer);

        $formStateResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(123);

        $originalResponse->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo(123));

        $formImplementation->processForm($formRuntime);
    }

    /**
     * @test
     */
    public function distributesFlashMessagesThatWereCollectedAlongsideFinishingProcess()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formState = $this->createMock(FormStateInterface::class);
        $finisherState = $this->createMock(FinisherStateInterface::class);
        $formImplementation = new FormImplementation($fusionRuntime, '', '');
        $controllerContext = $this->createMock(ControllerContext::class);
        $originalResponse = $this->createMock(Response::class);
        $formStateResponse = $this->createMock(Response::class);
        $originalFlashMessageContainer = $this->createMock(FlashMessageContainer::class);
        $formStateFlashMessageContainer = $this->createMock(FlashMessageContainer::class);
        $message1 = $this->createMock(Message::class);
        $message2 = $this->createMock(Message::class);

        $formRuntime->method('shouldProcess')->willReturn(true);
        $formRuntime->method('shouldValidate')->willReturn(true);
        $formRuntime->method('shouldRollback')->willReturn(false);
        $formRuntime->method('shouldFinish')->willReturn(true);
        $fusionRuntime->method('getControllerContext')->willReturn($controllerContext);
        $controllerContext->method('getResponse')->willReturn($originalResponse);
        $controllerContext->method('getFlashMessageContainer')->willReturn($originalFlashMessageContainer);
        $formRuntime->method('finish')->willReturn($finisherState);
        $finisherState->method('getResponse')->willReturn($formStateResponse);
        $finisherState->method('getFlashMessageContainer')->willReturn($formStateFlashMessageContainer);

        $formStateFlashMessageContainer->expects($this->once())
            ->method('getMessagesAndFlush')
            ->willReturn([$message1, $message2]);

        $originalFlashMessageContainer->expects($this->exactly(2))
            ->method('addMessage')
            ->withConsecutive([$this->identicalTo($message1)], [$this->identicalTo($message2)]);

        $formImplementation->processForm($formRuntime);
    }
}
