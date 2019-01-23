<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Pages;

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
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;

/**
 * Fusion object to create page definitions from collections
 */
class PageCollectionImplementation extends AbstractFusionObject
{
    /**
     * Returns itself for later evaluation
     *
     * @return array<PageDefinitionInterface>
     */
    public function evaluate()
    {
        $result = [];
        $collection = $this->fusionValue('collection');
        $itemName = $this->fusionValue('itemName');

        foreach ($collection as $item) {
            $pageDefinition = $this->renderPageDefinition($itemName, $item);

            $result[$pageDefinition->getName()] = $pageDefinition;
        }

        return $result;
    }

    /**
     * Render a single form page definition
     *
     * @param string $itemName
     * @param mixed $item
     * @return PageDefinitionInterface
     */
    protected function renderPageDefinition($itemName, $item)
    {
        $this->runtime->pushContext($itemName, $item);

        $pageDefinitionFactory = $this->runtime->evaluate(sprintf('%s/pageRenderer', $this->path), $this);

        $this->runtime->popContext();

        return $pageDefinitionFactory;
    }
}
