<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Pages;

use Neos\Flow\Tests\UnitTestCase;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Pages\PageListImplementation;

class PageListImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsListsOfPageDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $pageListImplementation = new PageListImplementation($fusionRuntime, '', '');

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
                $pageDefinition1,
                $pageDefinition2
            ));

        $pageDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn('pageName1');

        $pageDefinition2->expects($this->once())
            ->method('getName')
            ->willReturn('pageName2');

        $pageDefinitionMap = $pageListImplementation->evaluate();

        $this->assertSame($pageDefinition1, $pageDefinitionMap['pageName1']);
        $this->assertSame($pageDefinition2, $pageDefinitionMap['pageName2']);
    }

    /**
     * @test
     */
    public function doesNotRequireExplicitFusionObjectAssignmentsForPages()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $pageListImplementation = new PageListImplementation($fusionRuntime, '', '');

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
                $pageDefinition1,
                $pageDefinition2
            ));

        $pageDefinition1->expects($this->once())
            ->method('getName')
            ->willReturn('pageName1');

        $pageDefinition2->expects($this->once())
            ->method('getName')
            ->willReturn('pageName2');

        $pageDefinitionMap = $pageListImplementation->evaluate();

        $this->assertSame($pageDefinition1, $pageDefinitionMap['pageName1']);
        $this->assertSame($pageDefinition2, $pageDefinitionMap['pageName2']);
    }
}
