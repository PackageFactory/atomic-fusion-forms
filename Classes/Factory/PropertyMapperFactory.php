<?php
namespace PackageFactory\AtomicFusion\Forms\Factory;

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
use Neos\Flow\Property\PropertyMapper;

/**
 * @Flow\Scope("singleton")
 */
class PropertyMapperFactory
{
    /**
     * Create a new property mapper
     *
     * @return PropertyMapper
     */
    public function createPropertyMapper()
    {
        return new PropertyMapper();
    }
}
