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
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;

/**
 * Fusion object to create finisher definitions from collections
 */
class FinisherCollectionImplementation extends AbstractFusionObject
{
    /**
     * Returns a list of finisher definitions
     *
     * @return array<FinisherDefinitionInterface>
     */
    public function evaluate()
    {
        $result = [];
        $collection = $this->tsValue('collection');
        $itemName = $this->tsValue('itemName');

        foreach ($collection as $item) {
            $finisherDefinition = $this->renderFinisherDefinition($itemName, $item);

            $result[$finisherDefinition->getName()] = $finisherDefinition;
        }

        return $result;
    }

    /**
     * Render a single finisher definition
     *
     * @param string $itemName
     * @param mixed $item
     * @return FinisherDefinitionInterface
     */
    protected function renderFinisherDefinition($itemName, $item)
    {
        $this->tsRuntime->pushContextArray($this->tsRuntime->getCurrentContext() + [
            $itemName => $item
        ]);

        $finisherDefinition = $this->tsRuntime->evaluate(sprintf('%s/finisherRenderer', $this->path), $this);

        $this->tsRuntime->popContext();

        return $finisherDefinition;
    }
}
