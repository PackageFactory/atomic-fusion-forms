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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\PageDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\PageDefinitionFactoryInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\PageDefinitionMapFactoryInterface;

/**
 * Fusion object to create page definitions from collections
 */
class PageCollectionImplementation extends AbstractTypoScriptObject implements PageDefinitionMapFactoryInterface
{
    /**
     * Returns itself for later evaluation
     *
     * @return PageDefinitionMapFactoryInterface
     */
    public function evaluate()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createPageDefinitionMap(FormDefinitionInterface $formDefinition)
    {
        $result = [];
        $collection = $this->tsValue('collection');
        $itemName = $this->tsValue('itemName');

        foreach ($collection as $item) {
            $pageDefinitionFactory = $this->renderPageDefinitionFactory($itemName, $item);
            $pageDefinition = $pageDefinitionFactory->createPageDefinition($formDefinition);

            $result[$pageDefinition->getName()] = $pageDefinition;
        }

        return $result;
    }

    /**
     * Render a single form page definition factory
     *
     * @param string $itemName
     * @param mixed $item
     * @return PageDefinitionFactoryInterface
     */
    protected function renderPageDefinitionFactory($itemName, $item)
    {
        $this->tsRuntime->pushContextArray($this->tsRuntime->getCurrentContext() + [
            $itemName => $item
        ]);

        $pageDefinitionFactory = $this->tsRuntime->evaluate(sprintf('%s/pageRenderer', $this->path), $this);

        $this->tsRuntime->popContext();

        return $pageDefinitionFactory;
    }
}
