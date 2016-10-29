<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Finishers;

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
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finisher\FinisherInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;

/**
 * Fusion object to create finisher definitions
 */
class FinisherImplementation extends AbstractTypoScriptObject
{
    /**
     * Check if `implementationClassName` and `option` values are in order and return
     * a new FinisherDefinition based on these
     *
     * @return FinisherDefinitionInterface
     */
    public function evaluate()
    {
        if ($this->typoScriptObjectName === 'PackageFactory.AtomicFusion.Forms:Finisher') {
            throw new EvaluationException(
                'Please do not use `PackageFactory.AtomicFusion.Forms:Finisher` directly. ' .
                'You need to inherit from it and define the finisher implementation class name',
                1477759274
            );
        }

        $name = $this->tsValue('name');
        $implementationClassName = $this->tsValue('implementationClassName');
        $options = $this->tsValue('options');

        if (!$implementationClassName) {
            throw new EvaluationException(
                'You need to specify an implementation class name for a finisher.',
                1477759281
            );
        }

        if (!class_exists($implementationClassName)) {
            throw new EvaluationException(
                sprintf('Finisher class `%s` does not exist', $implementationClassName),
                1477759289
            );
        }

        if (!is_a($implementationClassName, FinisherInterface::class, true)) {
            throw new EvaluationException(
                sprintf(
                    'Finisher class `%s` does not implement `%s`',
                    $implementationClassName,
                    FinisherInterface::class
                ),
                1477759298
            );
        }

        if (!is_array($options)) {
            throw new EvaluationException(
                '`options` must be an array.',
                1477759306
            );
        }

        return new FinisherDefinition($name, $implementationClassName, $options);
    }
}
