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

use Neos\Flow\Annotations as Flow;
use Neos\Utility\ObjectAccess;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Validator\ValidatorInterface;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;

/**
 * @Flow\Scope("singleton")
 */
class ValidatorResolver implements ValidatorResolverInterface
{

    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @inheritdoc
     */
    public function resolve(ValidatorDefinitionInterface $validatorDefinition)
    {
        $implementationClassName = $validatorDefinition->getImplementationClassName();

        if (!$this->objectManager->isRegistered($implementationClassName)) {
            throw new ResolverException(
                sprintf('Error in ValidatorResolver: Class `%s` in unknown.', $implementationClassName),
                1497274278
            );
        }

        $validatorClassNames = static::getValidatorImplementationClassNames($this->objectManager);

        if (!array_key_exists($implementationClassName, $validatorClassNames)) {
            throw new ResolverException(
                sprintf(
                    'Error in ValidatorResolver: Class `%s` must implement ValidatorInterface.',
                    $implementationClassName
                ),
                1497274283
            );
        }

        $validator = $this->objectManager->get($implementationClassName);

        foreach ($validatorDefinition->getOptions() as $optionName => $optionValue) {
            if (!ObjectAccess::isPropertySettable($validator, $optionName)) {
                throw new ResolverException(
                    sprintf(
                        'Error in ValidatorResolver: Option `%s` is unknown to validator `%s`',
                        $optionName,
                        $implementationClassName
                    ),
                    1497337134
                );
            }
            ObjectAccess::setProperty($validator, $optionName, $optionValue);
        }

        return $validator;
    }

    /**
     * Returns all class names implementing the ValidatorInterface.
     *
     * @param ObjectManagerInterface $objectManager
     * @return array Array of class names implementing ValidatorInterface indexed by class name
     * @Flow\CompileStatic
     */
    public static function getValidatorImplementationClassNames($objectManager)
    {
        $reflectionService = $objectManager->get(ReflectionService::class);
        $classNames = $reflectionService->getAllImplementationClassNamesForInterface(ValidatorInterface::class);
        return array_flip($classNames);
    }
}
