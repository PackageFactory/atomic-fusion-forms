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

/**
 * Fusion object to create field definitions
 */
class FieldImplementation extends AbstractTypoScriptObject implements FieldDefinitionFactoryInterface
{
    /**
     * Returns itself for later evaluation
     *
     * @return FieldDefinitionFactoryInterface
     */
    public function evaluate()
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function createFieldDefinition(FormDefinitionInterface $formDefinition)
    {
        $fusionConfiguration = [
            'label' => $this->tsValue('label'),
            'name' => $this->tsValue('name'),
            'type' => $this->tsValue('type'),
            'page' => $this->tsValue('page')
        ];

        $processorDefinition = $this->tsValue('processor');
        $validatorDefinitions = $this->tsValue('validators');

        $fieldDefinition = new FieldDefinition($fusionConfiguration, $formDefinition);
        $fieldDefinition->setProcessorDefinition($processorDefinition);

        foreach ($validatorDefinitions as $validatorDefinition) {
            $fieldDefinition->addValidatorDefinition($validatorDefinition);
        }

        return $fieldDefinition;
    }
}
