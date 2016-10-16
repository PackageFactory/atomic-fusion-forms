<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Runtime;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Mvc\FlashMessageContainer;
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Http\Response;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FinisherRuntime;

class FinisherRuntimeTest extends UnitTestCase
{
    /**
     * @test
     */
    public function deliversResponse()
    {
        $response = $this->createMock(Response::class);

        $finisherRuntime = new FinisherRuntime($response);

        $this->assertSame($response, $finisherRuntime->getResponse());
    }

    /**
     * @test
     */
    public function deliversResult()
    {
        $response = $this->createMock(Response::class);
        $result = $this->createMock(Result::class);

        $finisherRuntime = new FinisherRuntime($response);
        $this->inject($finisherRuntime, 'result', $result);

        $this->assertSame($result, $finisherRuntime->getResult());
    }

    /**
     * @test
     */
    public function deliversFlashMessageContainer()
    {
        $response = $this->createMock(Response::class);
        $flashMessageContainer = $this->createMock(FlashMessageContainer::class);

        $finisherRuntime = new FinisherRuntime($response);
        $this->inject($finisherRuntime, 'flashMessageContainer', $flashMessageContainer);

        $this->assertSame($flashMessageContainer, $finisherRuntime->getFlashMessageContainer());
    }
}
