<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Context\Factory;

use TYPO3\Flow\Tests\UnitTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Context\Factory\FieldContextFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Context\FieldContext;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;

class FieldContextFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsFieldContexts()
    {
        $formRuntime = $this->createMock(FormRuntimeInterface::class);
        $fieldContextFactory = new FieldContextFactory();

        $fieldContext = $fieldContextFactory->createFieldContext($formRuntime, 'SomeName', 'SomePropertyPath');
        $this->assertTrue($fieldContext instanceof FieldContext);
    }
}
