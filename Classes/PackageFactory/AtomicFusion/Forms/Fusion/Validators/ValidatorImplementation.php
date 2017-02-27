<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Validators;

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
use Neos\Flow\Validation\Validator\ValidatorInterface;
use Neos\Fusion\TypoScriptObjects\AbstractTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Fusion\Exception\EvaluationException;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;

/**
 * Fusion object to create validator definitions
 */
class ValidatorImplementation extends AbstractTypoScriptObject
{
    /**
     * Check if `implementationClassName` and `option` values are in order and return
     * a new ValidatorDefinition based on these
     *
     * @return ValidatorDefinitionInterface
     */
    public function evaluate()
    {
        if ($this->typoScriptObjectName === 'PackageFactory.AtomicFusion.Forms:Validator') {
            throw new EvaluationException(
                'Please do not use `PackageFactory.AtomicFusion.Forms:Validator` directly. ' .
                'You need to inherit from it and define the validator implementation class name',
                1477754469
            );
        }

        $name = $this->tsValue('name');
        $implementationClassName = $this->tsValue('implementationClassName');
        $options = $this->tsValue('options');
        $customErrorMessage = $this->tsValue('message');

        if (!$implementationClassName) {
            throw new EvaluationException(
                'You need to specify an implementation class name for a validator.',
                1477754482
            );
        }

        if (!class_exists($implementationClassName)) {
            throw new EvaluationException(
                sprintf('Validator class `%s` does not exist', $implementationClassName),
                1477754490
            );
        }

        if (!is_a($implementationClassName, ValidatorInterface::class, true)) {
            throw new EvaluationException(
                sprintf(
                    'Validator class `%s` does not implement `%s`',
                    $implementationClassName,
                    ValidatorInterface::class
                ),
                1477754498
            );
        }

        if (!is_array($options)) {
            throw new EvaluationException(
                '`options` must be an array.',
                1477754506
            );
        }

        $validatorDefinition = new ValidatorDefinition($name, $implementationClassName, $options);
        $validatorDefinition->setCustomErrorMessage($customErrorMessage);

        return $validatorDefinition;
    }
}
