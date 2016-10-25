<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Context;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Error\Result;
use PackageFactory\AtomicFusion\Forms\Domain\Context\PageContext;
use PackageFactory\AtomicFusion\Forms\Domain\Context\Factory\FieldContextFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormStateInterface;

class PageContextTest extends UnitTestCase
{
    /**
     * @test
     */
    public function shouldDeliverLabel()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $pageDefinition = $this->createMock(PageDefinitionInterface::class);

        $formRuntime->expects($this->once())->method('getFormDefinition')->willReturn($formDefinition);
        $formDefinition->expects($this->once())
            ->method('getPageDefinition')
            ->with('ThePage')
            ->willReturn($pageDefinition);
        $pageDefinition->expects($this->once())->method('getLabel')->willReturn('TheLabel');

        $pageContext = new PageContext($formRuntime, 'ThePage');

        $this->assertEquals('TheLabel', $pageContext->getLabel());
    }

    /**
     * @test
     */
    public function shouldDeliverName()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $pageDefinition = $this->createMock(PageDefinitionInterface::class);

        $formRuntime->expects($this->once())->method('getFormDefinition')->willReturn($formDefinition);
        $formDefinition->expects($this->once())
            ->method('getPageDefinition')
            ->with('ThePage')
            ->willReturn($pageDefinition);
        $pageDefinition->expects($this->once())->method('getName')->willReturn('TheName');

        $pageContext = new PageContext($formRuntime, 'ThePage');

        $this->assertEquals('TheName', $pageContext->getName());
    }

    /**
     * @test
     */
    public function createsFieldContextForProperty()
    {
        $fieldContextFactory = $this->createMock(FieldContextFactory::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);

        $pageContext = new PageContext($formRuntime, '');

        $fieldContextFactory->expects($this->once())
            ->method('createFieldContext')
            ->with($formRuntime, 'name')
            ->WillReturn('TheFieldContext');

        $this->inject($pageContext, 'fieldContextFactory', $fieldContextFactory);

        $this->assertEquals('TheFieldContext', $pageContext->field('name'));
    }

    /**
     * @test
     */
    public function createsFieldContextForSubProperty()
    {
        $fieldContextFactory = $this->createMock(FieldContextFactory::class);
        $formRuntime = $this->createMock(FormRuntimeInterface::class);

        $pageContext = new PageContext($formRuntime, '');

        $fieldContextFactory->expects($this->once())
            ->method('createFieldContext')
            ->with($formRuntime, 'name', 'and.property.path')
            ->WillReturn('TheFieldContext');

        $this->inject($pageContext, 'fieldContextFactory', $fieldContextFactory);

        $this->assertEquals('TheFieldContext', $pageContext->field('name.and.property.path'));
    }

    /**
     * @test
     */
    public function shouldCheckIfItRepresentsTheCurrentPage()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $formState = $this->createMock(FormStateInterface::class);

        $formRuntime->expects($this->exactly(2))->method('getFormState')->willReturn($formState);
        $formState->expects($this->exactly(2))
            ->method('isCurrentPage')
            ->with('ThePage')
            ->will($this->onConsecutiveCalls(true, false));

        $pageContext1 = new PageContext($formRuntime, 'ThePage');
        $pageContext2 = new PageContext($formRuntime, 'ThePage');

        $this->assertTrue($pageContext1->isCurrentPage());
        $this->assertFalse($pageContext2->isCurrentPage());
    }

    /**
     * @test
     */
    public function allowsCallOfRelevantMethods()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $fieldContext = new PageContext($formRuntime, '');

        $this->assertTrue($fieldContext->allowsCallOfMethod('getLabel'));
        $this->assertTrue($fieldContext->allowsCallOfMethod('getName'));
        $this->assertTrue($fieldContext->allowsCallOfMethod('field'));
        $this->assertTrue($fieldContext->allowsCallOfMethod('isCurrentPage'));
    }
}
