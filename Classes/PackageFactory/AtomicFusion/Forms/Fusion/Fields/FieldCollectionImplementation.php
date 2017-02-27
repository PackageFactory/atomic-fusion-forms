<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Fields;

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
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;

/**
 * Fusion object to create field definitions from collections
 */
class FieldCollectionImplementation extends AbstractTypoScriptObject
{
    /**
     * Returns itself for later evaluation
     *
     * @return array<FieldDefinitionInterface>
     */
    public function evaluate()
    {
        $result = [];
        $collection = $this->tsValue('collection');
        $itemName = $this->tsValue('itemName');

        foreach ($collection as $item) {
            $fieldDefinition = $this->renderFieldDefinition($itemName, $item);

            $result[$fieldDefinition->getName()] = $fieldDefinition;
        }

        return $result;
    }

    /**
     * Render a single form field definition
     *
     * @param string $itemName
     * @param mixed $item
     * @return FieldDefinitionInterface
     */
    protected function renderFieldDefinition($itemName, $item)
    {
        $this->tsRuntime->pushContextArray($this->tsRuntime->getCurrentContext() + [
            $itemName => $item
        ]);

        $fieldDefinition = $this->tsRuntime->evaluate(sprintf('%s/fieldRenderer', $this->path), $this);

        $this->tsRuntime->popContext();

        return $fieldDefinition;
    }
}
