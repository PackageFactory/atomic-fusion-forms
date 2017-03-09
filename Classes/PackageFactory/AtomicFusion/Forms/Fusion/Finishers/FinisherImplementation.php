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

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException;
use PackageFactory\AtomicFusion\Forms\Fusion\Traits\InferNameFromPathTrait;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finisher\FinisherInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;

/**
 * Fusion object to create finisher definitions
 */
class FinisherImplementation extends AbstractFusionObject implements FinisherDefinitionInterface
{
    use InferNameFromPathTrait;

    /**
     * @var FinisherDefinitionInterface
     */
    protected $resolvedFinisherDefinition;

    /**
     * @var array
     */
    protected $initialFusionContext;

    /**
     * Returns itself for later evaluation
     *
     * @return FinisherDefinitionInterface
     */
    public function evaluate()
    {
        if ($this->fusionObjectName === 'PackageFactory.AtomicFusion.Forms:Finisher') {
            throw new EvaluationException(
                'Please do not use `PackageFactory.AtomicFusion.Forms:Finisher` directly. ' .
                'You need to inherit from it and define the finisher implementation class name',
                1477759274
            );
        }

        $this->initialFusionContext = $this->runtime->getCurrentContext() ?: [];

        return $this;
    }

    /**
     * Check if `implementationClassName` and `option` values are in order and return
     * a new FinisherDefinition based on these
     *
     * @return FinisherDefinitionInterface
     */
    protected function resolveFinisherDefinition()
    {
        if ($this->resolvedFinisherDefinition) {
            return $this->resolvedFinisherDefinition;
        }

        /*
         * The form property is taken from the latest context and combined
         * with the context during the initial evaluation.
         *
         * @todo use the form propertyName that is defined in fusion instead of 'form'
         */
        $combinedFusionContext = $this->initialFusionContext;
        $context = $this->runtime->getCurrentContext();
        $formContextName = 'form';

        if ($context[$formContextName]) {
            $combinedFusionContext[$formContextName] = $context[$formContextName];
        }

        $this->runtime->pushContextArray($combinedFusionContext);

        $implementationClassName = $this->tsValue('implementationClassName');
        $options = $this->tsValue('options');

        $this->runtime->popContext();

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

        $this->resolvedFinisherDefinition = new FinisherDefinition(
            $this->getName(),
            $implementationClassName,
            $options
        );
        return $this->resolvedFinisherDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getImplementationClassName()
    {
        return $this->resolveFinisherDefinition()->getImplementationClassName();
    }

    /**
     *@inheritdoc
     */
    public function getOptions()
    {
        return $this->resolveFinisherDefinition()->getOptions();
    }
}
