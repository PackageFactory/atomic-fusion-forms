<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Functional\SimpleCase;

require_once(__DIR__ . '/../BaseTestCase.php');

use PackageFactory\AtomicFusion\Forms\Tests\Functional\BaseTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormState;

class SimpleCaseTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldRenderTheFormCorrectly()
    {
        $request = $this->buildInitialRequest();
        $view = $this->buildView($request, __DIR__);

        $result = $view->render();

        $this->assertContains('<input type="text" name="--simpleForm[someString]" />', $result);
    }

    /**
     * @test
     */
    public function shouldDisplayASimpleMessageAfterFinishing()
    {
        $request = $this->buildRequest(
            'simpleForm',
            ['someString' => 'Some Value'],
            ['someString'],
            new FormState()
        );
        $view = $this->buildView($request, __DIR__);

        $result = $view->render();

        $this->assertEquals('Result: Some Value', $result);
    }
}
