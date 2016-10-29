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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\TypoScript\TypoScriptObjects\AbstractTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\FieldDefinitionFactoryInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\FieldDefinitionMapFactoryInterface;

/**
 * Fusion object to create field definitions from collections
 */
class FieldCollectionImplementation extends AbstractTypoScriptObject implements FieldDefinitionMapFactoryInterface
{
    /**
     * Returns itself for later evaluation
     *
     * @return FieldDefinitionMapFactoryInterface
     */
    public function evaluate()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createFieldDefinitionMap(FormDefinitionInterface $formDefinition)
    {
        $result = [];
        $collection = $this->tsValue('collection');
        $itemName = $this->tsValue('itemName');

        foreach ($collection as $item) {
            $fieldDefinitionFactory = $this->renderFieldDefinitionFactory($itemName, $item);
            $fieldDefinition = $fieldDefinitionFactory->createFieldDefinition($formDefinition);

            $result[$fieldDefinition->getName()] = $fieldDefinition;
        }

        return $result;
    }

    /**
     * Render a single form field definition factory
     *
     * @param string $itemName
     * @param mixed $item
     * @return FieldDefinitionFactoryInterface
     */
    protected function renderFieldDefinitionFactory($itemName, $item)
    {
        $this->tsRuntime->pushContextArray($this->tsRuntime->getCurrentContext() + [
            $itemName => $item
        ]);

        $fieldDefinitionFactory = $this->tsRuntime->evaluate(sprintf('%s/fieldRenderer', $this->path), $this);

        $this->tsRuntime->popContext();

        return $fieldDefinitionFactory;
    }
}
