<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\State;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Mvc\FlashMessageContainer;
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Http\Response;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FinisherState;

class FinisherStateTest extends UnitTestCase
{
    /**
     * @test
     */
    public function deliversResponse()
    {
        $response = $this->createMock(Response::class);

        $finisherState = new FinisherState($response);

        $this->assertSame($response, $finisherState->getResponse());
    }

    /**
     * @test
     */
    public function deliversResult()
    {
        $response = $this->createMock(Response::class);
        $result = $this->createMock(Result::class);

        $finisherState = new FinisherState($response);
        $this->inject($finisherState, 'result', $result);

        $this->assertSame($result, $finisherState->getResult());
    }

    /**
     * @test
     */
    public function deliversFlashMessageContainer()
    {
        $response = $this->createMock(Response::class);
        $flashMessageContainer = $this->createMock(FlashMessageContainer::class);

        $finisherState = new FinisherState($response);
        $this->inject($finisherState, 'flashMessageContainer', $flashMessageContainer);

        $this->assertSame($flashMessageContainer, $finisherState->getFlashMessageContainer());
    }
}
