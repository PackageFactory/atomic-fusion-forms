<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Fusion\Pages;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\TypoScript\Core\Runtime as FusionRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\PageDefinitionFactoryInterface;
use PackageFactory\AtomicFusion\Forms\Fusion\Pages\PageImplementation;

class PageImplementationTest extends UnitTestCase
{
    /**
     * @test
     */
    public function evaluatesToAFormDefinitionFactory()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $pageImplementation = new PageImplementation($fusionRuntime, '', '');

        $this->assertTrue($pageImplementation->evaluate() instanceof PageDefinitionFactoryInterface);
    }

    /**
     * @test
     */
    public function createsPageDefinitions()
    {
        $fusionRuntime = $this->createMock(FusionRuntime::class);
        $pageImplementation = new PageImplementation($fusionRuntime, '', '');
        $formDefinition = $this->createMock(FormDefinitionInterface::class);

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

        $pageDefinition = $pageImplementation->createPageDefinition($formDefinition);

        $this->assertTrue($pageDefinition instanceof PageDefinitionInterface);
        $this->assertEquals('SomeLabel', $pageDefinition->getLabel());
        $this->assertEquals('SomeName', $pageDefinition->getName());
    }
}
