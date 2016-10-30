<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Model\Definition;

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

/**
 * Defines methods for field definitions
 */
interface FieldDefinitionInterface
{
    /**
     * Get the field label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get the field name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the target type
     *
     * @return string
     */
    public function getType();

    /**
     * Get the string reference to the page, where this field is
     * supposed to be used
     *
     * @return string
     */
    public function getPage();

    /**
     * Get the processor definition for this field
     *
     * @return ProcessorDefinitionInterface
     */
    public function getProcessorDefinition();


    /**
     * Get the validator definitions for this field
     *
     * @return array<ValidatorDefinitionInterface>
     */
    public function getValidatorDefinitions();

    /**
     * Get a single validator definition addressed by its name
     *
     * @param string $name
     * @return ValidatorDefinitionInterface
     * @throws DefinitionException when no validator can be found under the given name
     */
    public function getValidatorDefinition($name);

    /**
     * Set the owning form definition
     *
     * @param FormDefinitionInterface $formDefinition
     * @return void
     */
    public function setFormDefinition(FormDefinitionInterface $formDefinition);

    /**
     * Get the owning form definition
     *
     * @return FormDefinitionInterface
     */
    public function getFormDefinition();
}
