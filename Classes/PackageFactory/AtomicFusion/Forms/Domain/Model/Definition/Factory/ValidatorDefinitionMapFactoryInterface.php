<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory;

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

interface ValidatorDefinitionMapFactoryInterface
{
    /**
     * Create a new ValidatorDefinition
     *
     * @return array<ValidatorDefinitionInterface>
     */
    public function createValidatorDefinitionMap();
}
