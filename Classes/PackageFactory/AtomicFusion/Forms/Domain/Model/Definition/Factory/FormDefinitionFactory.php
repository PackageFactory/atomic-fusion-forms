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
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;

/**
 * Create form definitions
 *
 * @Flow\Scope("singleton")
 * @codeCoverageIgnore
 */
class FormDefinitionFactory
{
    /**
     * Create a new form definition
     *
     * @param array $fusionConfiguration
     * @return FormDefinitionInterface
     */
    public function createFormDefinition(array $fusionConfiguration)
    {
        return new FormDefinition($fusionConfiguration);
    }
}
