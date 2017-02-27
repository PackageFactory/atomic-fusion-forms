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
use Neos\Fusion\FusionObjects\AbstractArrayFusionObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinition;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;

/**
 * Fusion object to create lists field definitions
 */
class FieldListImplementation extends AbstractArrayFusionObject
{
    /**
     * Returns itself for later evaluation
     *
     * @return array<FieldDefinitionInterface>
     */
    public function evaluate()
    {
        $result = [];

        foreach ($this->properties as $key => $configuration) {
            $fieldDefinition = $this->renderFieldDefinition($key, $configuration);

            $result[$fieldDefinition->getName()] = $fieldDefinition;
        }

        return $result;
    }

    /**
     * Render a single form field definition
     *
     * @param string $key
     * @param array $configuration
     * @return FieldDefinitionInterface
     */
    protected function renderFieldDefinition($key, $configuration)
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
