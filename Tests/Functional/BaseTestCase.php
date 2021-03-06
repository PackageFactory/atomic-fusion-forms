<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Functional;

use Neos\Flow\Http\Request;
use Neos\Flow\Mvc\ActionResponse as Response;
use Neos\Flow\Http\Uri;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Mvc\Controller\Arguments;
use Neos\Flow\Mvc\Controller\ControllerContext;
use Neos\Flow\Mvc\Routing\UriBuilder;
use Neos\Flow\Tests\FunctionalTestCase;
use Neos\Fusion\View\FusionView;
use PackageFactory\AtomicFusion\Forms\Domain\Service\State\FormState;
use PackageFactory\AtomicFusion\Forms\Service\CryptographyService;
use PackageFactory\AtomicFusion\Forms\Service\PropertyMappingConfigurationService;

/**
 * Testcase for the TypoScript View
 *
 */
abstract class BaseTestCase extends FunctionalTestCase
{
    /**
     * Helper to build a TypoScript view object
     *
     * @return FusionView
     */
    protected function buildView(Request $httpRequest, $fusionPathPattern)
    {
        $view = new FusionView();

        $request = new ActionRequest($httpRequest);
        $request->setArguments($httpRequest->getArguments());

        $uriBuilder = new UriBuilder();
        $uriBuilder->setRequest($request);

        $controllerContext = new ControllerContext(
            $request,
            new Response(),
            new Arguments(array()),
            $uriBuilder
        );

        $view->setControllerContext($controllerContext);
        $view->disableFallbackView();
        $view->setPackageKey('PackageFactory.AtomicFusion.Forms');
        $view->setFusionPath('form');
        $view->setFusionPathPatterns([
            getcwd() . '/Build/Travis/Packages/Application/Neos.Fusion/Resources/Private/Fusion',
            getcwd() . '/Build/Travis/Packages/Application/PackageFactory.AtomicFusion.Forms/Resources/Private/Fusion/Forms',
            $fusionPathPattern
        ]);

        return $view;
    }

    /**
     * Build a dummy GET request
     *
     * @return Request
     */
    protected function buildInitialRequest()
    {
        $uri = new Uri('http://example.com/my-form');
        return Request::create($uri, 'GET', [], [], []);
    }

    /**
     * Build a dummy POST request
     *
     * @param string $argumentNamespace
     * @param array $arguments
     * @param array $trustedProperties
     * @param FormState $formState
     * @return Request
     */
    protected function buildRequest(
        $argumentNamespace,
        array $arguments = [],
        array $trustedProperties = [],
        FormState $formState = null
    )
    {
        $uri = new Uri('http://example.com/my-form');
        $cryptographyService = new CryptographyService();
        $propertyMappingConfigurationService = new PropertyMappingConfigurationService();

        return Request::create($uri, 'POST', [
            sprintf('--%s', $argumentNamespace) => array_merge(
                [
                    '__state' => $cryptographyService->encodeHiddenFormMetadata(
                        $formState
                    ),
                    '__trustedProperties' => $propertyMappingConfigurationService
                        ->generateTrustedPropertiesToken(
                            $trustedProperties
                        )
                ],
                $arguments
            )
        ], [], []);
    }
}
