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
use TYPO3\Flow\Reflection\ObjectAccess;
use TYPO3\Flow\Object\ObjectManagerInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finishers\FinisherInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException;

/**
 * @Flow\Scope("singleton")
 */
class FinisherResolver implements FinisherResolverInterface
{
    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @inheritdoc
     */
    public function resolve(FinisherDefinitionInterface $finisherDefinition)
    {
        $implementationClassName = $finisherDefinition->getImplementationClassName();

        if (!$this->objectManager->isRegistered($implementationClassName)) {
            throw new ResolverException(
                sprintf('Error in FinisherResolver: Class `%s` in unknown.', $implementationClassName),
                1476611095
            );
        }

        $finisherClassNames = static::getFinisherImplementationClassNames($this->objectManager);

        if (!array_key_exists($implementationClassName, $finisherClassNames)) {
            throw new ResolverException(
                sprintf(
                    'Error in FinisherResolver: Class `%s` must implement FinisherInterface.',
                    $implementationClassName
                ),
                1476611105
            );
        }

        $finisher = $this->objectManager->get($implementationClassName);

        foreach ($finisherDefinition->getOptions() as $optionName => $optionValue) {
            if (!ObjectAccess::isPropertySettable($finisher, $optionName)) {
                throw new ResolverException(
                    sprintf(
                        'Error in FinisherResolver: Option `%s` is unknown to finisher `%s`',
                        $optionName,
                        $implementationClassName
                    ),
                    1476624534
                );
            }

            ObjectAccess::setProperty($finisher, $optionName, $optionValue);
        }

        return $finisher;
    }

    /**
     * Returns all class names implementing the FinisherInterface.
     *
     * @param ObjectManagerInterface $objectManager
     * @return array Array of class names implementing FinisherInterface indexed by class name
     * @Flow\CompileStatic
     */
    protected static function getFinisherImplementationClassNames(ObjectManagerInterface $objectManager)
    {
        $reflectionService = $objectManager->get(ReflectionService::class);
        $classNames = $reflectionService->getAllImplementationClassNamesForInterface(FinisherInterface::class);

        return array_flip($classNames);
    }
}
