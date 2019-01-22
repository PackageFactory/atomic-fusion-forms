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

use Neos\Flow\Annotations as Flow;

/**
 * Defines methods for page definitions
 */
interface PageDefinitionInterface
{
    /**
     * Get the page label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get the page name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the field definitions of this page
     *
     * @return array<FieldDefinitionInterface>
     */
    public function getFieldDefinitions();

    /**
     * Get a single field definition addressed by its name
     *
     * @param string $name
     * @return FieldDefinitionInterface
     * @throws DefinitionException when no field can be found under the given name
     */
    public function getFieldDefinition($name);

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
