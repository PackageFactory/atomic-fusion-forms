<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Pages;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\PageDefinitionMapFactoryInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\PageDefinitionFactoryInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Pages\PageListImplementation;

class PageListImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function evaluatesToAFormDefinitionMapFactory()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $pageListImplementation = new PageListImplementation($fusionRuntime, '', '');

        $this->assertTrue($pageListImplementation->evaluate() instanceof PageDefinitionMapFactoryInterface);
    }

    /**
     * @test
     */
    public function createsListsOfPageDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $pageListImplementation = new PageListImplementation($fusionRuntime, '', '');

        $pageDefinitionFactory1 = $this->createMock(PageDefinitionFactoryInterface::class);
        $pageDefinitionFactory2 = $this->createMock(PageDefinitionFactoryInterface::class);
        $pageDefinition1 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition2 = $this->createMock(PageDefinitionInterface::class);

        $this->inject($pageListImplementation, 'properties', [
            'page1' => [
                '__objectType' => 'SomeFusionObject'
            ],
            'page2' => [
                '__objectType' => 'SomeOtherFusionObject'
            ]
        ]);

        $fusionRuntime->expects($this->exactly(2))
            ->method('render')
            ->withConsecutive(['/page1'], ['/page2'])
            ->will($this->onConsecutiveCalls(
                $pageDefinitionFactory1,
                $pageDefinitionFactory2
            ));

        $pageDefinitionFactory1->expects($this->once())
            ->method('createPageDefinition')
            ->with($formDefinition)
            ->willReturn($pageDefinition1);

        $pageDefinitionFactory2->expects($this->once())
            ->method('createPageDefinition')
            ->with($formDefinition)
            ->willReturn($pageDefinition2);

        $pageDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn('pageName1');

        $pageDefinition2->expects($this->once())
            ->method('getName')
            ->willReturn('pageName2');

        $pageDefinitionMap = $pageListImplementation->createPageDefinitionMap($formDefinition);

        $this->assertSame($pageDefinition1, $pageDefinitionMap['pageName1']);
        $this->assertSame($pageDefinition2, $pageDefinitionMap['pageName2']);
    }

    /**
     * @test
     */
    public function doesNotRequireExplicitFusionObjectAssignmentsForPages()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $pageListImplementation = new PageListImplementation($fusionRuntime, '', '');

        $pageDefinitionFactory1 = $this->createMock(PageDefinitionFactoryInterface::class);
        $pageDefinitionFactory2 = $this->createMock(PageDefinitionFactoryInterface::class);
        $pageDefinition1 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition2 = $this->createMock(PageDefinitionInterface::class);

        $this->inject($pageListImplementation, 'properties', [
            'page1' => [],
            'page2' => []
        ]);

        $fusionRuntime->expects($this->exactly(2))
            ->method('render')
            ->withConsecutive(
                ['/page1<PackageFactory.AtomicFusion.Forms:Page>'],
                ['/page2<PackageFactory.AtomicFusion.Forms:Page>']
            )
            ->will($this->onConsecutiveCalls(
                $pageDefinitionFactory1,
                $pageDefinitionFactory2
            ));

        $pageDefinitionFactory1->expects($this->once())
            ->method('createPageDefinition')
            ->with($formDefinition)
            ->willReturn($pageDefinition1);

        $pageDefinitionFactory2->expects($this->once())
            ->method('createPageDefinition')
            ->with($formDefinition)
            ->willReturn($pageDefinition2);

        $pageDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn('pageName1');

        $pageDefinition2->expects($this->once())
            ->method('getName')
            ->willReturn('pageName2');

        $pageDefinitionMap = $pageListImplementation->createPageDefinitionMap($formDefinition);

        $this->assertSame($pageDefinition1, $pageDefinitionMap['pageName1']);
        $this->assertSame($pageDefinition2, $pageDefinitionMap['pageName2']);
    }
}
