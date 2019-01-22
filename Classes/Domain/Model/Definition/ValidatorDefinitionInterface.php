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
 * Defines methods for validator definitions
 */
interface ValidatorDefinitionInterface
{
    /**
     * Get the name of this validator
     *
     * @return string
     */
    public function getName();

    /**
     * Get the implementation class name for this validator
     *
     * @return string
     */
    public function getImplementationClassName();

    /**
     * Get the options for this validator
     *
     * @return array
     */
    public function getOptions();

    /**
     * Get the user-defined custom error message for this validator
     *
     * @return string
     */
    public function getCustomErrorMessage();


    /**
     * Checker whether this validator was configured with a custom error message
     *
     * @return boolean
     */
    public function hasCustomErrorMessage();
}
