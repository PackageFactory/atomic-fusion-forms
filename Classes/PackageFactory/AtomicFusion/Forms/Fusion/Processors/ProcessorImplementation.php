<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Processors;

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
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Processor\ProcessorInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;

/**
 * Fusion object to create processor definitions
 */
class ProcessorImplementation extends AbstractFusionObject
{
    /**
     * Check if `implementationClassName` and `option` values are in order and return
     * a new ProcessorDefinition based on these
     *
     * @return ProcessorDefinitionInterface
     */
    public function evaluate()
    {
        if ($this->typoScriptObjectName === 'PackageFactory.AtomicFusion.Forms:Processor') {
            throw new EvaluationException(
                'Please do not use `PackageFactory.AtomicFusion.Forms:Processor` directly. ' .
                'You need to inherit from it and define the processor implementation class name',
                1477740676
            );
        }

        $implementationClassName = $this->tsValue('implementationClassName');
        $options = $this->tsValue('options');

        if (!$implementationClassName) {
            throw new EvaluationException(
                'You need to specify an implementation class name for a processor.',
                1477740801
            );
        }

        if (!class_exists($implementationClassName)) {
            throw new EvaluationException(
                sprintf('Processor class `%s` does not exist', $implementationClassName),
                1477741130
            );
        }

        if (!is_a($implementationClassName, ProcessorInterface::class, true)) {
            throw new EvaluationException(
                sprintf(
                    'Processor class `%s` does not implement `%s`',
                    $implementationClassName,
                    ProcessorInterface::class
                ),
                1477741143
            );
        }

        if (!is_array($options)) {
            throw new EvaluationException(
                '`options` must be an array.',
                1477741187
            );
        }

        return new ProcessorDefinition($implementationClassName, $options);
    }
}
