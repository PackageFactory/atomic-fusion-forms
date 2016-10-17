<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task;

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
 * Method definitions for process task
 */
interface ProcessTaskInterface
{
    /**
     * Process the given arguments by their field definitions and write possibly occuring messages
     * to the given validation result
     *
     * @param PropertyMappingConfiguration $propertyMappingConfiguration
     * @param FieldDefinitionInterface $fieldDefinition
     * @param mixed $input
     * @param Result $validationResult
     * @return array The processed arguments
     */
    public function run(
        PropertyMappingConfiguration $propertyMappingConfiguration,
        FieldDefinitionInterface $fieldDefinition,
        $input,
        Result $validationResult
    );
}
