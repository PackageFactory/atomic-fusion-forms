<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Functional\MultiPage;

require_once(__DIR__ . '/../BaseTestCase.php');

use PackageFactory\AtomicFusion\Forms\Tests\Functional\BaseTestCase;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormState;

class MultiPageCaseTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldRenderTheFirstPageCorrectly()
    {
        $request = $this->buildInitialRequest();
        $view = $this->buildView($request, __DIR__);

        $result = $view->render();

        $this->assertContains('<input type="text" name="--simpleForm[stringOnFirstPage]" />', $result);
    }

    /**
     * @test
     */
    public function shouldRenderTheSecondPageCorrectly()
    {
        $formState = new FormState();
        $formState->setCurrentPage('firstPage');

        $request = $this->buildRequest(
            'simpleForm',
            ['stringOnFirstPage' => 'Some Value 1'],
            ['stringOnFirstPage'],
            $formState
        );
        $view = $this->buildView($request, __DIR__);

        $result = $view->render();

        $this->assertContains('<input type="text" name="--simpleForm[stringOnSecondPage]" />', $result);
    }

    /**
     * @test
     */
    public function shouldDisplayASimpleMessageAfterFinishing()
    {
        $formState = new FormState();
        $formState->setCurrentPage('secondPage');
        $formState->addValue('stringOnFirstPage', 'Some Value 1');

        $request = $this->buildRequest(
            'simpleForm',
            ['stringOnSecondPage' => 'Some Value 2'],
            ['stringOnSecondPage'],
            $formState
        );
        $view = $this->buildView($request, __DIR__);

        $result = $view->render();

        $this->assertEquals('Result: Some Value 1, Some Value 2', $result);
    }
}
