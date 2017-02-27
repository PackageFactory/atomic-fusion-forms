<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Pages;

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
use Neos\Fusion\TypoScriptObjects\AbstractTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;

/**
 * Fusion object to create field definitions
 */
class PageImplementation extends AbstractTypoScriptObject
{
    /**
     * Returns itself for later evaluation
     *
     * @return PageDefinitionFactoryInterface
     */
    public function evaluate()
    {
        $fusionConfiguration = [
            'label' => $this->tsValue('label'),
            'name' => $this->tsValue('name')
        ];

        return new PageDefinition($fusionConfiguration);
    }
}
