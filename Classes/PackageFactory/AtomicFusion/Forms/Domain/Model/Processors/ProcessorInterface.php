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
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;

/**
 * Defines methods for processors
 */
interface ProcessorInterface
{
    /**
     * Apply this processor onto user input
     *
     * @param PropertyMappingConfiguration $propertyMappingConfiguration
     * @param Result $result
     * @param FieldDefinitionInterface $fieldDefinition
     * @param array $options
     * @param mixed $input The user input value
     * @return mixed A sanitized, converted and ready-to-persist value
     */
    public function apply(
        PropertyMappingConfiguration $propertyMappingConfiguration,
        Result $result,
        FieldDefinitionInterface $fieldDefinition,
        array $options,
        $input
    );

    /**
     * Whenever something goes wrong during form processing, this method can be used to clean up and remove unwanted
     * side effects that occurred during processing (e.g. remove created resources)
     *
     * @param PropertyMappingConfiguration $propertyMappingConfiguration
     * @param Result $result
     * @param FieldDefinitionInterface $fieldDefinition
     * @param array $options
     * @param mixed $input The user input value
     * @param mixed The sanitized, converted and ready-to-persist value, generated in `apply`
     * @return void
     */
    public function rollback(
        PropertyMappingConfiguration $propertyMappingConfiguration,
        Result $result,
        FieldDefinitionInterface $fieldDefinition,
        array $options,
        $input,
        $value
    );
}
