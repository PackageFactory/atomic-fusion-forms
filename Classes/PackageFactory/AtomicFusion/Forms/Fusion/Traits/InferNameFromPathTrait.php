<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Traits;

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
 * Trait for use in Fusion objects to infer their name by their path, if
 * not explicitly set as a value
 */
trait InferNameFromPathTrait
{
    /**
     * @var string
     */
    protected $cachedName;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        if ($name = $this->cachedName) {
            return $name;
        }

        if ($name = $this->tsValue('name')) {
            return $this->cachedName = $name;
        }

        //
        // Infer name from path
        //
        $pathParts = explode('/', $this->path);
        $namePart = array_pop($pathParts);
        list($name) = explode('<', $namePart);

        return $this->cachedName = $name;
    }
}
