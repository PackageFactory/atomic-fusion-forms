<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Model\Processor;

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
use Neos\Error\Messages\Result;
use Neos\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Factory\PropertyMapperFactory;
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
     * @codeCoverageIgnore
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
