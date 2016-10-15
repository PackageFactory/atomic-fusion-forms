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
 * Defines methods for processor definitions
 */
interface FinisherDefinitionInterface
{
    /**
     * Get the name for this finisher
     *
     * @return string
     */
    public function getName();

    /**
     * Get the implementation class name for this finisher
     *
     * @return string
     */
    public function getImplementationClassName();

    /**
     * Get the options for this finisher
     *
     * @return array
     */
    public function getOptions();
}
