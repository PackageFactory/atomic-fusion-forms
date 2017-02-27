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
use PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException;

/**
 * Defines methods for form definitions
 */
interface FormDefinitionInterface
{
    /**
     * Get the form label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get the form name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the form action
     *
     * @return string
     */
    public function getAction();

    /**
     * Add a new field definition
     *
     * @param FieldDefinitionInterface $fieldDefinition
     * @return void
     */
    public function addFieldDefinition(FieldDefinitionInterface $fieldDefinition);

    /**
     * Get the field definitions of this form
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
     * Add a new finisher definition
     *
     * @param FinisherDefinitionInterface $finisherDefinition
     * @return void
     */
    public function addFinisherDefinition(FinisherDefinitionInterface $finisherDefinition);

    /**
     * Get the finisher definitions of this form
     *
     * @return array<FinisherDefinitionInterface>
     */
    public function getFinisherDefinitions();

    /**
     * Get a single finisher definition addressed by its name
     *
     * @param string $name
     * @return FinisherDefinitionInterface
     * @throws DefinitionException when no finisher can be found under the given name
     */
    public function getFinisherDefinition($name);

    /**
     * Add a new page definition
     *
     * @param PageDefinitionInterface $pageDefinition
     * @return void
     */
    public function addPageDefinition(PageDefinitionInterface $pageDefinition);

    /**
     * Get the page definitions of this form
     *
     * @return array<PageDefinitionInterface>
     */
    public function getPageDefinitions();

    /**
     * Get a single page definition addressed by its name
     *
     * @param string $name
     * @return PageDefinitionInterface
     * @throws DefinitionException when no page can be found under the given name
     */
    public function getPageDefinition($name);

    /**
     * Get the next page relative to the given page
     *
     * @param string $currentPage
     * @return string
     * @throws DefinitionException when no page can be found under the given name
     */
    public function getNextPage($currentPage);

    /**
     * Check if this form has pages
     *
     * @return boolean
     */
    public function hasPages();

}
