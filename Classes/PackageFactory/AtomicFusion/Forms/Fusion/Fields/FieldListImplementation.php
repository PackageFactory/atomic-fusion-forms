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
use TYPO3\TypoScript\TypoScriptObjects\AbstractArrayTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\FieldDefinitionFactoryInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\Factory\FieldDefinitionMapFactoryInterface;

/**
 * Fusion object to create field definitions
 */
class FieldListImplementation extends AbstractArrayTypoScriptObject implements FieldDefinitionMapFactoryInterface
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

        foreach ($this->properties as $key => $configuration) {
            $fieldDefinitionFactory = $this->renderFieldDefinitionFactory($key, $configuration);
            $fieldDefinition = $fieldDefinitionFactory->createFieldDefinition($formDefinition);

            $result[$fieldDefinition->getName()] = $fieldDefinition;
        }

        return $result;
    }

    /**
     * Render a single form field definition factory
     *
     * @param string $key
     * @param array $configuration
     * @return FieldDefinitionFactoryInterface
     */
    protected function renderFieldDefinitionFactory($key, $configuration)
    {
        if (isset($configuration['__objectType'])) {
            return $this->tsRuntime->render(
                sprintf('%s/%s', $this->path, $key)
            );
        }

        return $this->tsRuntime->render(
            sprintf('%s/%s<PackageFactory.AtomicFusion.Forms:Field>', $this->path, $key)
        );
    }
}
