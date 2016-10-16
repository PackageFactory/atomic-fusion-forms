<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver;

/**
 * This file is part of the PackageFactory.AtomicFusion.Forms package
 *
 * (c) 2016 Wilhelm Behncke <wilhelm.behncke@googlemail.com>
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Reflection\ReflectionService;
use TYPO3\Flow\Object\ObjectManagerInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Processors\ProcessorInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException;

/**
 * @Flow\Scope("singleton")
 */
class ProcessorResolver implements ProcessorResolverInterface
{
    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @inheritdoc
     */
    public function resolve(ProcessorDefinitionInterface $processorDefinition)
    {
        $implementationClassName = $processorDefinition->getImplementationClassName();

        if (!$this->objectManager->isRegistered($implementationClassName)) {
            throw new ResolverException(
                sprintf('Error in ProcessorResolver: Class `%s` in unknown.', $implementationClassName),
                1476599710
            );
        }

        $processorClassNames = static::getProcessorImplementationClassNames($this->objectManager);

        if (!array_key_exists($implementationClassName, $processorClassNames)) {
            throw new ResolverException(
                sprintf(
                    'Error in ProcessorResolver: Class `%s` must implement ProcessorInterface.',
                    $implementationClassName
                ),
                1476599826
            );
        }

        return $this->objectManager->get($implementationClassName);
    }

    /**
     * Returns all class names implementing the ProcessorInterface.
     *
     * @param ObjectManagerInterface $objectManager
     * @return array Array of class names implementing ProcessorInterface indexed by class name
     * @Flow\CompileStatic
     */
    public static function getProcessorImplementationClassNames($objectManager)
    {
        $reflectionService = $objectManager->get(ReflectionService::class);
        $classNames = $reflectionService->getAllImplementationClassNamesForInterface(ProcessorInterface::class);
        return array_flip($classNames);
    }
}
