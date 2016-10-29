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
use PackageFactory\AtomicFusion\Forms\Fusion\Pages\PageCollectionImplementation;

class PageCollectionImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function evaluatesToAFormDefinitionMapFactory()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $pageListImplementation = new PageCollectionImplementation($fusionRuntime, '', '');

        $this->assertTrue($pageListImplementation->evaluate() instanceof PageDefinitionMapFactoryInterface);
    }

    /**
     * @test
     */
    public function createsListsOfPageDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $formDefinition = $this->createMock(FormDefinitionInterface::class);
        $pageListImplementation = new PageCollectionImplementation($fusionRuntime, '', '');

        $pageDefinitionFactory1 = $this->createMock(PageDefinitionFactoryInterface::class);
        $pageDefinitionFactory2 = $this->createMock(PageDefinitionFactoryInterface::class);
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
                $pageDefinitionFactory1,
                $pageDefinitionFactory2
            ));

        $fusionRuntime->expects($this->exactly(2))
            ->method('pushContextArray')
            ->withConsecutive(
                [['TheItemName' => 'Item1']],
                [['TheItemName' => 'Item2']]
            );

        $fusionRuntime->expects($this->exactly(2))->method('popContext');

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
