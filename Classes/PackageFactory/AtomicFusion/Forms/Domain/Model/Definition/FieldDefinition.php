<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Model\Definition;

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

/**
 * Runtime form field definition
 */
class FieldDefinition implements FieldDefinitionInterface
{
    /**
     * @var array
     */
    protected $fusionConfiguration;

    /**
     * @var ProcessorDefinitionInterface
     */
    protected $resolvedProcessorDefinition = null;

    /**
     * @var array<ValidatorDefinitionInterface>
     */
    protected $resolvedValidatorDefinitions = null;

    /**
     * Constructor
     *
     * @param array $fusionConfiguration
     */
    public function __construct(array $fusionConfiguration)
    {
        $this->fusionConfiguration = $fusionConfiguration;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return Arrays::getValueByPath($this->fusionConfiguration, 'label')
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Arrays::getValueByPath($this->fusionConfiguration, 'name')
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return Arrays::getValueByPath($this->fusionConfiguration, 'type')
    }

    /**
     * @inheritdoc
     */
    public function getPage()
    {
        return Arrays::getValueByPath($this->fusionConfiguration, 'page')
    }

    /**
     * @inheritdoc
     */
    public function getProcessorDefinition()
    {
        if ($this->resolvedProcessorDefinition === null) {
            // TODO: Resolve processor definition
        }

        return $this->resolvedProcessorDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorDefinitions()
    {
        if ($this->resolvedValidatorDefinitions === null) {
            // TODO: Resolve processor definition
        }

        return $this->resolvedValidatorDefinitions;
    }
}
