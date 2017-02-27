<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Context\Factory;

use Neos\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Context\Factory\PageContextFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Context\PageContext;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;

class PageContextFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsPageContexts()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $fieldContextFactory = new PageContextFactory();

        $fieldContext = $fieldContextFactory->createPageContext($formRuntime, 'SomeName');
        $this->assertTrue($fieldContext instanceof PageContext);
    }
}
