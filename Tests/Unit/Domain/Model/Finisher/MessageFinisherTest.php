<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Finisher;

use PackageFactory\AtomicFusion\Forms\Domain\Model\Finisher\MessageFinisher;

class MessageFinisherTest extends FinisherTestCase
{
    /**
     * @test
     */
    public function addsConfiguredMessageToResponseWithNoOtherSideEffects()
    {
        $finisher = new MessageFinisher();
        $finisher->setMessage('SomeMessage');

        $this->executeFinisher($finisher);

        $this->assertResponseEquals('SomeMessage');
        $this->assertResultIsEmpty();
        $this->assertFlashMessagesAreEmpty();
    }

    /**
     * @test
     */
    public function acceptsObjectsWithToStringMethodAsMessage()
    {
        $toStringClassInstance = new __toStringClass__1476737147;

        $finisher = new MessageFinisher();
        $finisher->setMessage($toStringClassInstance);

        $this->executeFinisher($finisher);

        $this->assertResponseEquals('SomeString');
        $this->assertResultIsEmpty();
        $this->assertFlashMessagesAreEmpty();
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\FinisherStateException
     * @expectedExceptionCode 1476546610
     */
    public function complainsIfNonStringInputIsProvidedAsMessage()
    {
        $finisher = new MessageFinisher();
        $finisher->setMessage(new \stdClass);

        $this->executeFinisher($finisher);
    }

    /**
     * @test
     * @expectedException \PackageFactory\AtomicFusion\Forms\Domain\Exception\FinisherStateException
     * @expectedExceptionCode 1476546610
     */
    public function complainsIfMessageIsNotSet()
    {
        $finisher = new MessageFinisher();
        $this->executeFinisher($finisher);
    }
}
