<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Model\Finishers;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Mvc\FlashMessageContainer;
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Http\Response;
use PackageFactory\AtomicFusion\Forms\Domain\Service\FinisherRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finishers\FinisherInterface;

class __toStringClass { public function __toString() { return 'SomeString'; } }

abstract class FinisherTestCase extends UnitTestCase
{
    protected $finisherRuntime = null;

    /**
     * @before
     */
    public function mockFinisherRuntime()
    {
        $flashMessageContainer = new FlashMessageContainer();
        $result = new Result();
        $response = new Response();

        $this->finisherRuntime = $this->createMock(FinisherRuntime::class);
        $this->finisherRuntime->method('getFlashMessageContainer')->willReturn($flashMessageContainer);
        $this->finisherRuntime->method('getResult')->willReturn($result);
        $this->finisherRuntime->method('getResponse')->willReturn($response);
    }

    /**
     * @after
     */
    public function destroyFinisherRuntime()
    {
        $this->finisherRuntime = null;
    }

    /**
     * Execute the finisher with the mocked finisher runtime
     *
     * @param FinisherInterface $finisher
     * @return void
     */
    protected function executeFinisher(FinisherInterface $finisher)
    {
        $finisher->execute($this->finisherRuntime);
    }

    /**
     * Assert that after execution of the finisher, the response equals a certain value
     *
     * @param string $content
     * @return void
     */
    protected function assertResponseEquals($content)
    {
        $this->assertEquals($content, $this->finisherRuntime->getResponse()->getContent());
    }

    /**
     * Assert that after execution of the finisher, the response remains empty
     *
     * @return void
     */
    protected function assertResponseIsEmpty()
    {
        $this->assertEquals('', $this->finisherRuntime->getResponse()->getContent());
    }

    /**
     * Assert that after execution of the finisher, the flash messages remain empty
     *
     * @param string $content
     * @return void
     */
    protected function assertFlashMessagesAreEmpty()
    {
        $this->assertEquals([], $this->finisherRuntime->getFlashMessageContainer()->getMessages());
    }

    /**
     * Assert that after execution of the finisher, the result remains empty
     *
     * @return void
     */
    protected function assertResultIsEmpty()
    {
        $this->assertFalse($this->finisherRuntime->getResult()->hasMessages());
    }
}
