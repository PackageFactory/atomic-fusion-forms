<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Model\Processors;

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
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Domain\Factory\PropertyMapperFactory;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;

/**
 * @Flow\Scope("singleton")
 */
class DefaultProcessor implements ProcessorInterface
{
    /**
     * @Flow\Inject
     * @var PropertyMapperFactory
     */
    protected $propertyMapperFactory;

    /**
     * @inheritdoc
     */
     public function apply(
         PropertyMappingConfiguration $propertyMappingConfiguration,
         Result $result,
         FieldDefinitionInterface $fieldDefinition,
         array $options,
         $input
    )
    {
        if ($type = $fieldDefinition->getType()) {
            $propertyMapper = $this->propertyMapperFactory->createPropertyMapper();
            $value = $propertyMapper->convert($input, $type, $propertyMappingConfiguration);
            $result->merge($propertyMapper->getMessages());
            return $value;
        }

        return $input;
    }

    /**
     * @inheritdoc
     */
     public function rollback(
         PropertyMappingConfiguration $propertyMappingConfiguration,
         Result $result,
         FieldDefinitionInterface $fieldDefinition,
         array $options,
         $input,
         $value
    )
    {
        //
        // Nothing to do here
        //
    }
}
