<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Pages;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Fusion\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Pages\PageImplementation;

class PageImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsPageDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $pageImplementation = new PageImplementation($fusionRuntime, '', '');

        $fusionRuntime->expects($this->exactly(2))
            ->method('evaluate')
            ->withConsecutive(
                ['/label', $pageImplementation],
                ['/name', $pageImplementation]
            )
            ->will($this->onConsecutiveCalls(
                'SomeLabel',
                'SomeName'
            ));

        $pageDefinition = $pageImplementation->evaluate();

        $this->assertTrue($pageDefinition instanceof PageDefinitionInterface);
        $this->assertEquals('SomeLabel', $pageDefinition->getLabel());
        $this->assertEquals('SomeName', $pageDefinition->getName());
    }
}
