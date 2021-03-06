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
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;

/**
 * Fusion object to create validator definitions from collections
 */
class ValidatorCollectionImplementation extends AbstractFusionObject
{
    /**
     * Returns a list of validator definitions
     *
     * @return array<ValidatorDefinitionInterface>
     */
    public function evaluate()
    {
        $result = [];
        $collection = $this->fusionValue('collection');
        $itemName = $this->fusionValue('itemName');

        foreach ($collection as $item) {
            $validatorDefinition = $this->renderValidatorDefinition($itemName, $item);

            $result[$validatorDefinition->getName()] = $validatorDefinition;
        }

        return $result;
    }

    /**
     * Render a single validator definition
     *
     * @param string $itemName
     * @param mixed $item
     * @return ValidatorDefinitionInterface
     */
    protected function renderValidatorDefinition($itemName, $item)
    {
        $this->runtime->pushContext($itemName, $item);

        $validatorDefinition = $this->runtime->evaluate(sprintf('%s/validatorRenderer', $this->path), $this);

        $this->runtime->popContext();

        return $validatorDefinition;
    }
}
