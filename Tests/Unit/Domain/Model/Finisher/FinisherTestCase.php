<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Finisher;

use Neos\Flow\Tests\UnitTestCase;
use Neos\Flow\Mvc\FlashMessage\FlashMessageContainer;
use Neos\Error\Messages\Result;
use Neos\Flow\Http\Response;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FinisherStateInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finisher\FinisherInterface;

class __toStringClass__1476737147 { public function __toString() { return 'SomeString'; } }

abstract class FinisherTestCase extends UnitTestCase
{
    protected $finisherState = null;

    /**
     * @before
     */
    public function mockFinisherState()
    {
        $flashMessageContainer = new FlashMessageContainer();
        $result = new Result();
        $response = new Response();

        $this->finisherState = $this->createMock(FinisherStateInterface::class);
        $this->finisherState->method('getFlashMessageContainer')->willReturn($flashMessageContainer);
        $this->finisherState->method('getResult')->willReturn($result);
        $this->finisherState->method('getResponse')->willReturn($response);
    }

    /**
     * @after
     */
    public function destroyFinisherState()
    {
        $this->finisherState = null;
    }

    /**
     * Execute the finisher with the mocked finisher runtime
     *
     * @param FinisherInterface $finisher
     * @return void
     */
    protected function executeFinisher(FinisherInterface $finisher)
    {
        $finisher->execute($this->finisherState);
    }

    /**
     * Assert that after execution of the finisher, the response equals a certain value
     *
     * @param string $content
     * @return void
     */
    protected function assertResponseEquals($content)
    {
        $this->assertEquals($content, $this->finisherState->getResponse()->getContent());
    }

    /**
     * Assert that after execution of the finisher, the response remains empty
     *
     * @return void
     */
    protected function assertResponseIsEmpty()
    {
        $this->assertEquals('', $this->finisherState->getResponse()->getContent());
    }

    /**
     * Assert that after execution of the finisher, the flash messages remain empty
     *
     * @param string $content
     * @return void
     */
    protected function assertFlashMessagesAreEmpty()
    {
        $this->assertEquals([], $this->finisherState->getFlashMessageContainer()->getMessages());
    }

    /**
     * Assert that after execution of the finisher, the result remains empty
     *
     * @return void
     */
    protected function assertResultIsEmpty()
    {
        $this->assertFalse($this->finisherState->getResult()->hasMessages());
    }
}
