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
     * @var FormDefinitionInterface
     */
    protected $formDefinition;

    /**
     * @var ProcessorDefinitionInterface
     */
    protected $processorDefinition = null;

    /**
     * @var array<ValidatorDefinitionInterface>
     */
    protected $validatorDefinitions = [];

    /**
     * Constructor
     *
     * @param array $fusionConfiguration
     * @param FormDefinitionInterface $formDefinition
     */
    public function __construct(array $fusionConfiguration, FormDefinitionInterface $formDefinition)
    {
        $this->fusionConfiguration = $fusionConfiguration;
        $this->formDefinition = $formDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return Arrays::getValueByPath($this->fusionConfiguration, 'label');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return Arrays::getValueByPath($this->fusionConfiguration, 'name');
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return Arrays::getValueByPath($this->fusionConfiguration, 'type');
    }

    /**
     * @inheritdoc
     */
    public function getPage()
    {
        return Arrays::getValueByPath($this->fusionConfiguration, 'page');
    }

    /**
     * Set the processor definition
     *
     * @param ProcessorDefinitionInterface $processorDefinition
     * @return void
     */
    public function setProcessorDefinition(ProcessorDefinitionInterface $processorDefinition)
    {
        $this->processorDefinition = $processorDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getProcessorDefinition()
    {
        return $this->processorDefinition;
    }

    /**
     * Add a new validator definition
     *
     * @param ValidatorDefinitionInterface $validatorDefinition
     * @return void
     */
    public function addValidatorDefinition(ValidatorDefinitionInterface $validatorDefinition)
    {
        $this->validatorDefinitions[$validatorDefinition->getName()] = $validatorDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorDefinitions()
    {
        return $this->validatorDefinitions;
    }

    /**
     * @inheritdoc
     */
    public function getValidatorDefinition($name)
    {
        if (array_key_exists($name, $this->validatorDefinitions)) {
            return $this->validatorDefinitions[$name];
        }

        throw new DefinitionException(
            sprintf('Could not find validator definition for `%s` in field `%s`', $name, $this->getName()),
            1476539849
        );
    }

    /**
     * @inheritdoc
     */
    public function getFormDefinition()
    {
        return $this->formDefinition;
    }
}
