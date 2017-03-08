<?php
namespace PackageFactory\AtomicFusion\Forms\Service;

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
 * @Flow\Scope("singleton")
 */
class HiddenInputTagMappingService
{
    /**
     * Converts flat associative arrays to a string containing hidden input fields
     *
     * @param array  $map
     * @param string $fieldNamePrefix [description]
     * @return string
     */
    public function convertFlatMapToHiddenInputTags(array $map, $fieldNamePrefix)
    {
        $result = '';
        foreach ($map as $key => $value) {
            if (is_array($value) || (is_object($value) && !method_exists($value, 'toString'))) {
                throw new \Exception('The given map needs to be flat.', 1475408307);
            }

            $result .= sprintf(
                '<input type="hidden" name="%s" value="%s" />',
                sprintf('%s[%s]', $fieldNamePrefix, $key),
                htmlspecialchars($value)
            );
        }

        return $result;
    }
}
