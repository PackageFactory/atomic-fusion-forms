<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Finisher;

use TYPO3\Flow\Log\SystemLoggerInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finisher\LogFinisher;

class LogFinisherTest extends FinisherTestCase
{
    /**
     * @test
     */
    public function logsConfiguredMessageWithConfiguredSeverityWithNoFurtherSideEffects()
    {
        $logger = $this->createMock(SystemLoggerInterface::class);

        $finisher = new LogFinisher();
        $finisher->setMessage('SomeMessage');
        $finisher->setSeverity('warning');

        $this->inject($finisher, 'logger', $logger);

        $logger->expects($this->once())
            ->method('log')
            ->with('SomeMessage', LOG_WARNING);

        $this->executeFinisher($finisher);

        $this->assertResponseIsEmpty();
        $this->assertResultIsEmpty();
        $this->assertFlashMessagesAreEmpty();
    }

    /**
     * @test
     */
    public function fallsBackToInfoSeverityIfNoSeverityIsProvided()
    {
        $logger = $this->createMock(SystemLoggerInterface::class);

        $finisher = new LogFinisher();
        $finisher->setMessage('SomeMessage');

        $this->inject($finisher, 'logger', $logger);

        $logger->expects($this->once())
            ->method('log')
            ->with('SomeMessage', LOG_INFO);

        $this->executeFinisher($finisher);

        $this->assertResponseIsEmpty();
        $this->assertResultIsEmpty();
        $this->assertFlashMessagesAreEmpty();
    }

    /**
     * @test
     */
    public function acceptsObjectsWithToStringMethodAsMessage()
    {
        $toStringClassInstance = new __toStringClass__1476737147;

        $logger = $this->createMock(SystemLoggerInterface::class);

        $finisher = new LogFinisher();
        $finisher->setSeverity('warning');
        $finisher->setMessage($toStringClassInstance);

        $this->inject($finisher, 'logger', $logger);

        $logger->expects($this->once())
            ->method('log')
            ->with('SomeString');

        $this->executeFinisher($finisher);

        $this->assertResponseIsEmpty();
        $this->assertResultIsEmpty();
        $this->assertFlashMessagesAreEmpty();
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\FinisherStateException
     * @expectedExceptionCode 1476546610
     */
    public function complainsIfSeverityIsInvalid()
    {
        $finisher = new LogFinisher();
        $finisher->setSeverity('DefinitelyInvalid');

        $this->executeFinisher($finisher);
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\FinisherStateException
     * @expectedExceptionCode 1476563413
     */
    public function complainsIfNonStringInputIsProvidedAsMessage()
    {
        $finisher = new LogFinisher();
        $finisher->setMessage(new \stdClass);
        $finisher->setSeverity('warning');

        $this->executeFinisher($finisher);
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\FinisherStateException
     * @expectedExceptionCode 1476563413
     */
    public function complainsIfMessageIsNotSet()
    {
        $finisher = new LogFinisher();
        $finisher->setSeverity('warning');

        $this->executeFinisher($finisher);
    }
}
