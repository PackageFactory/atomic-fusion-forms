<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Functional\SimpleCase;

require_once(__DIR__ . '/../BaseTestCase.php');

use PackageFactory\AtomicFusion\Forms\Tests\Functional\BaseTestCase;
use TYPO3\Flow\Package\PackageManagerInterface;

class SimpleCaseTest extends BaseTestCase
{
    /**
     * @test
     */
    public function shouldRenderTHeFormCorrectly()
    {
        $request = $this->buildInitialRequest();
        $view = $this->buildView($request, __DIR__);

        $result = $view->render();

        $this->assertContains('<input type="text" name="--[someString]" />', $result);
    }
}
