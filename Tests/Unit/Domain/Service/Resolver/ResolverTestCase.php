<?php
namespace PackageFactory\AtomicFusion\Forms\Tests\Unit\Domain\Service\Resolver;

use TYPO3\Flow\Tests\UnitTestCase;
use TYPO3\Flow\ObjectManagement\ObjectManagerInterface;
use TYPO3\Flow\Reflection\ReflectionService;

abstract class ResolverTestCase extends UnitTestCase
{
    /**
     * Inject an object manager that will always return the string class name for the requested
     * instance.
     *
     * @param mixed $resolver
     * @param array $validClassNames
     * @param array $invalidClassNames
     * @return void
     */
    protected function injectObjectManager(
        $resolver,
        array $validClassNames = [],
        array $invalidClassNames = []
    )
    {
        $reflectionService = $this->createMock(ReflectionService::class);
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $returnMap = [];

        $returnMap[] = [ReflectionService::class, $reflectionService];

        if (is_a(current($validClassNames), static::RESOLVER_TARGET_CLASS)) {
            $reflectionService->method('getAllImplementationClassNamesForInterface')
                ->with(static::RESOLVER_TARGET_CLASS)->willReturn(array_keys($validClassNames));
        } else {
            $reflectionService->method('getAllImplementationClassNamesForInterface')
                ->with(static::RESOLVER_TARGET_CLASS)->willReturn($validClassNames);
        }

        foreach ($validClassNames as $key => $value) {
            if (is_a($value, static::RESOLVER_TARGET_CLASS)) {
                $instance = $value;
                $validClassName = $key;
            } else {
                $instance = $this->getMockBuilder(static::RESOLVER_TARGET_CLASS)
                    ->setMethods(array_merge(
                        ['getClassNameForTestPurposes'],
                        get_class_methods(static::RESOLVER_TARGET_CLASS)
                    ))
                    ->getMock();
                $instance->method('getClassNameForTestPurposes')->willReturn($value);

                $validClassName = $value;
            }

            $returnMap[] = [$validClassName, $instance];
        }

        foreach ($invalidClassNames as $invalidClassName) {
            $returnMap[] = [$invalidClassName, new \stdClass];
        }

        $objectManager->method('isRegistered')->will($this->returnCallback(
            function ($name) use ($validClassNames, $invalidClassNames) {
                return in_array($name, $validClassNames) || array_key_exists($name, $validClassNames) ||
                    in_array($name, $invalidClassNames);
            }
        ));
        $objectManager->method('get')->will($this->returnValueMap($returnMap));

        $this->inject($resolver, 'objectManager', $objectManager);
    }
}
