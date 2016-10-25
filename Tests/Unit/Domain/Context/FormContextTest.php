<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Context;

use TYPO3\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Context\FormContext;
use PackageFactory\AtomicFusion\Forms\Domain\Context\Factory\FieldContextFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Context\Factory\PageContextFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormStateInterface;

class FormContextTest extends UnitTestCase
{
    /**
     * @test
     */
    public function deliversLabel()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

        $formRuntime->expects($this->once())->method('getFormDefinition')->willReturn($formDefinition);
        $formDefinition->expects($this->once())
            ->method('getLabel')
            ->willReturn('TheLabel');

        $formContext = new FormContext($formRuntime);

        $this->assertEquals('TheLabel', $formContext->getLabel());
    }

    /**
     * @test
     */
    public function deliversName()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

        $formRuntime->expects($this->once())->method('getFormDefinition')->willReturn($formDefinition);
        $formDefinition->expects($this->once())
            ->method('getName')
            ->willReturn('TheName');

        $formContext = new FormContext($formRuntime);

        $this->assertEquals('TheName', $formContext->getName());
    }

    /**
     * @test
     */
    public function createsFieldContextForProperty()
    {
        $fieldContextFactory = $this->createMock(FieldContextFactory::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);

        $formContext = new FormContext($formRuntime);

        $fieldContextFactory->expects($this->once())
            ->method('createFieldContext')
            ->with($formRuntime, 'name')
            ->WillReturn('TheFieldContext');

        $this->inject($formContext, 'fieldContextFactory', $fieldContextFactory);

        $this->assertEquals('TheFieldContext', $formContext->field('name'));
    }

    /**
     * @test
     */
    public function createsFieldContextForSubProperty()
    {
        $fieldContextFactory = $this->createMock(FieldContextFactory::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);

        $formContext = new FormContext($formRuntime);

        $fieldContextFactory->expects($this->once())
            ->method('createFieldContext')
            ->with($formRuntime, 'name', 'and.property.path')
            ->WillReturn('TheFieldContext');

        $this->inject($formContext, 'fieldContextFactory', $fieldContextFactory);

        $this->assertEquals('TheFieldContext', $formContext->field('name.and.property.path'));
    }

    /**
     * @test
     */
    public function createsPageContext()
    {
        $pageContextFactory = $this->createMock(PageContextFactory::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);

        $formContext = new FormContext($formRuntime);

        $pageContextFactory->expects($this->once())
            ->method('createPageContext')
            ->with($formRuntime, 'pageName')
            ->WillReturn('ThePageContext');

        $this->inject($formContext, 'pageContextFactory', $pageContextFactory);

        $this->assertEquals('ThePageContext', $formContext->page('pageName'));
    }

    /**
     * @test
     */
    public function allowsCallOfRelevantMethods()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formContext = new FormContext($formRuntime);

        $this->assertTrue($formContext->allowsCallOfMethod('getLabel'));
        $this->assertTrue($formContext->allowsCallOfMethod('getName'));
        $this->assertTrue($formContext->allowsCallOfMethod('field'));
        $this->assertTrue($formContext->allowsCallOfMethod('page'));
    }
}
