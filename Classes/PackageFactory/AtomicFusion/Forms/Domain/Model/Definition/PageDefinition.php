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

use Neos\Flow\Annotations as Flow;
use Neos\Utility\Arrays;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException;

/**
 * Runtime page definition
 */
class PageDefinition implements PageDefinitionInterface
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
    protected $resolvedFieldDefinitions = null;

    /**
     * Constructor
     *
     * @param array $fusionConfiguration
     * @param FormDefinitionInterface $formDefinition
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
    public function getFieldDefinitions()
    {
        if ($this->resolvedFieldDefinitions === null) {
            $name = $this->getName();

            $fieldDefinitions = array_filter(
                $this->formDefinition->getFieldDefinitions(),
                function ($fieldDefinition) use ($name) {
                    return $fieldDefinition->getPage() === $name;
                }
            );

            foreach ($fieldDefinitions as $fieldDefinition) {
                $this->resolvedFieldDefinitions[$fieldDefinition->getName()] = $fieldDefinition;
            }
        }

        return $this->resolvedFieldDefinitions;
    }

    /**
     * @inheritdoc
     */
    public function getFieldDefinition($name)
    {
        $fieldDefinitions = $this->getFieldDefinitions();

        if ($fieldDefinitions && array_key_exists($name, $fieldDefinitions)) {
            return $fieldDefinitions[$name];
        }

        throw new DefinitionException(
            sprintf('Could not find field definition for `%s` in page `%s`', $name, $this->getName()),
            1476537396
        );
    }

    /**
     * @inheritdoc
     */
    public function setFormDefinition(FormDefinitionInterface $formDefinition)
    {
        $this->formDefinition = $formDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getFormDefinition()
    {
        return $this->formDefinition;
    }
}
