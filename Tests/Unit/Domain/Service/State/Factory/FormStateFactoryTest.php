<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\State\Factory;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\Mvc\ActionRequest;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormStateInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\Factory\FormStateFactory;
use PackageFactory\AtomicFusion\Forms\Service\CryptographyService;

class FormStateFactoryTest extends UnitTestCase
{
    /**
     * @test
     */
    public function createsFormStates()
    {
        $formStateFactory = new FormStateFactory();

        $formState = $formStateFactory->createFormState();

        $this->assertTrue($formState instanceof FormStateInterface);
    }

    /**
     * @test
     */
    public function createsFormStatesFromActionRequest()
    {
        $formStateFactory = new FormStateFactory();
        $mockFormState = $this->createMock(FormStateInterface::class);
        $actionRequest1 = $this->createMock(ActionRequest::class);
        $actionRequest2 = $this->createMock(ActionRequest::class);
        $cryptographyService = $this->createMock(CryptographyService::class);

        $actionRequest1->expects($this->once())
            ->method('getInternalArgument')
            ->with('__state')
            ->willReturn(null);

        $actionRequest2->expects($this->once())
            ->method('getInternalArgument')
            ->with('__state')
            ->willReturn('TheSerializedState');

        $cryptographyService->expects($this->once())
            ->method('decodeHiddenFormMetadata')
            ->with('TheSerializedState')
            ->willReturn($mockFormState);

        $this->inject($formStateFactory, 'cryptographyService', $cryptographyService);

        $formState1 = $formStateFactory->createFromActionRequest($actionRequest1);
        $formState2 = $formStateFactory->createFromActionRequest($actionRequest2);

        $this->assertTrue($formState1 instanceof FormStateInterface);
        $this->assertTrue($formState2 instanceof FormStateInterface);

        $this->assertNotSame($mockFormState, $formState1);
        $this->assertSame($mockFormState, $formState2);
    }
}
