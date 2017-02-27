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
use Neos\Fusion\FusionObjects\AbstractArrayFusionObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;

/**
 * Fusion object to create lists of validator definitions
 */
class ValidatorListImplementation extends AbstractArrayFusionObject
{
    /**
     * Returns a list of validator definitions
     *
     * @return array<ValidatorDefinitionInterface>
     */
    public function evaluate()
    {
        $result = [];

        foreach (array_keys($this->properties) as $key) {
            $validatorDefinition = $this->renderValidatorDefinition($key);

            $result[$validatorDefinition->getName()] = $validatorDefinition;
        }

        return $result;
    }

    /**
     * Render a single validator definition
     *
     * @param string $key
     * @return ValidatorDefinitionInterface
     */
    protected function renderValidatorDefinition($key)
    {
        return $this->tsRuntime->render(
            sprintf('%s/%s', $this->path, $key)
        );
    }
}
