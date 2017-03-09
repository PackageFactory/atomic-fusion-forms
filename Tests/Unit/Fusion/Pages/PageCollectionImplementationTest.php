<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Pages;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Fusion\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Pages\PageCollectionImplementation;

class PageCollectionImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsListsOfPageDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $pageListImplementation = new PageCollectionImplementation($fusionRuntime, '', '');

        $pageDefinition1 = $this->createMock(PageDefinitionInterface::class);
        $pageDefinition2 = $this->createMock(PageDefinitionInterface::class);

        $fusionRuntime->method('getCurrentContext')->willReturn([]);

        $fusionRuntime->expects($this->exactly(4))
            ->method('evaluate')
            ->withConsecutive(
                ['/collection', $pageListImplementation],
                ['/itemName', $pageListImplementation],
                ['/pageRenderer', $pageListImplementation],
                ['/pageRenderer', $pageListImplementation]
            )
            ->will($this->onConsecutiveCalls(
                ['Item1', 'Item2'],
                'TheItemName',
                $pageDefinition1,
                $pageDefinition2
            ));

        $fusionRuntime->expects($this->exactly(2))
            ->method('pushContext')
            ->withConsecutive(
                ['TheItemName', 'Item1'],
                ['TheItemName', 'Item2']
            );

        $fusionRuntime->expects($this->exactly(2))->method('popContext');

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
