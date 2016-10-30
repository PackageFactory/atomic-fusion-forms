<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Functional\NotEmptyValidatorCase;

require_once(__DIR__ . '/../BaseTestCase.php');

use PackageFactory\AtomicFusion\Forms\Tests\Functional\BaseTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormState;

class NotEmptyValidatorCaseTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldRenderTheFormCorrectly()
    {
        $request = $this->buildInitialRequest();
        $view = $this->buildView($request, __DIR__);

        $result = $view->render();

        $this->assertContains('<input type="text" name="--simpleForm[someRequiredString]" />', $result);
    }

    /**
     * @test
     */
    public function shouldDisplayASimpleMessageAfterFinishing()
    {
        $request = $this->buildRequest(
            'simpleForm',
            ['someRequiredString' => 'Some Value'],
            ['someRequiredString'],
            new FormState()
        );
        $view = $this->buildView($request, __DIR__);

        $result = $view->render();

        $this->assertEquals('Result: Some Value', $result);
    }

    /**
     * @test
     */
    public function shouldDisplayTheValidatorErrorMessageIfArgumentIsEmpty()
    {
        $request = $this->buildRequest(
            'simpleForm',
            ['someRequiredString' => ''],
            ['someRequiredString'],
            new FormState()
        );
        $view = $this->buildView($request, __DIR__);

        $result = $view->render();

        $this->assertContains('This field is required.', $result);
    }
}
