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
class FormAugmentationService
{
    /**
     * Add string information to the beginning of a form
     *
     * @param  string $formString
     * @param  string $toBeInjectedString
     * @return string
     */
    public function injectStringAfterOpeningFormTag($formString, $toBeInjectedString)
    {
        $openingBracketPosition = -1;
        $closingBracketPosition = -1;

        while (($nextOpeningBracketPosition = strpos($formString, '<', $openingBracketPosition + 1)) !== false) {
            if (strtolower(substr($formString, $nextOpeningBracketPosition, 5)) === '<form') {
                $closingBracketPosition = strpos($formString, '>', $nextOpeningBracketPosition);
                break;
            }

            $openingBracketPosition = $nextOpeningBracketPosition;
        }

        $preInjectionString = substr($formString, 0, $closingBracketPosition + 1);
        $postInjectionString = substr($formString, $closingBracketPosition + 1);

        return $preInjectionString . $toBeInjectedString . $postInjectionString;
    }
}
