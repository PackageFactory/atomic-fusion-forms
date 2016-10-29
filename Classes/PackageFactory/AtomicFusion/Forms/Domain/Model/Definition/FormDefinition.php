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
use TYPO3\Flow\Utility\Arrays;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\DefinitionException;

/**
 * Runtime form definition
 */
class FormDefinition implements FormDefinitionInterface
{
    /**
     * @var array
     */
    protected $fusionConfiguration;

    /**
     * @var array<FieldDefinitionInterface>
     */
    protected $fieldDefinitions = [];

    /**
     * @var array<FinisherDefinitionInterface>
     */
    protected $finisherDefinitions = [];

    /**
     * @var array<PageDefinitionInterface>
     */
    protected $pageDefinitions = [];

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
    public function getAction()
    {
        return Arrays::getValueByPath($this->fusionConfiguration, 'action');
    }

    /**
     * Add a new field definition
     *
     * @param FieldDefinitionInterface $fieldDefinition
     * @return void
     */
    public function addFieldDefinition(FieldDefinitionInterface $fieldDefinition)
    {
        if ($fieldDefinition->getFormDefinition() !== $this) {
            throw new DefinitionException(
                sprintf('Field `%s` does not belong to form `%s`', $fieldDefinition->getName(), $this->getName()),
                1476539967
            );
        }

        $this->fieldDefinitions[$fieldDefinition->getName()] = $fieldDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getFieldDefinitions()
    {
        return $this->fieldDefinitions;
    }

    /**
     * @inheritdoc
     */
    public function getFieldDefinition($name)
    {
        if (array_key_exists($name, $this->fieldDefinitions)) {
            return $this->fieldDefinitions[$name];
        }

        throw new DefinitionException(
            sprintf('Could not find field definition for `%s` in form `%s`', $name, $this->getName()),
            1476536391
        );
    }

    /**
     * Add a new finisher definition
     *
     * @param FinisherDefinitionInterface $finisherDefinition
     * @return void
     */
    public function addFinisherDefinition(FinisherDefinitionInterface $finisherDefinition)
    {
        $this->finisherDefinitions[$finisherDefinition->getName()] = $finisherDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getFinisherDefinitions()
    {
        return $this->finisherDefinitions;
    }

    /**
     * @inheritdoc
     */
    public function getFinisherDefinition($name)
    {
        if (array_key_exists($name, $this->finisherDefinitions)) {
            return $this->finisherDefinitions[$name];
        }

        throw new DefinitionException(
            sprintf('Could not find finisher definition for `%s` in form `%s`', $name, $this->getName()),
            1476536944
        );
    }

    /**
     * Add a new page definition
     *
     * @param PageDefinitionInterface $pageDefinition
     * @return void
     */
    public function addPageDefinition(PageDefinitionInterface $pageDefinition)
    {
        if ($pageDefinition->getFormDefinition() !== $this) {
            throw new DefinitionException(
                sprintf('Page `%s` does not belong to form `%s`', $pageDefinition->getName(), $this->getName()),
                1476540007
            );
        }

        $this->pageDefinitions[$pageDefinition->getName()] = $pageDefinition;
    }

    /**
     * @inheritdoc
     */
    public function getPageDefinitions()
    {
        return $this->pageDefinitions;
    }

    /**
     * @inheritdoc
     */
    public function getPageDefinition($name)
    {
        if (array_key_exists($name, $this->pageDefinitions)) {
            return $this->pageDefinitions[$name];
        }

        throw new DefinitionException(
            sprintf('Could not find page definition for `%s` in form `%s`', $name, $this->getName()),
            1476536979
        );
    }

    /**
     * @inheritdoc
     */
    public function getNextPage($currentPage)
    {
        if (!array_key_exists($name, $this->pageDefinitions)) {
            throw new DefinitionException(
                sprintf('Could not find page definition for `%s` in form `%s`', $name, $this->getName()),
                1477775697
            );
        }

        $pageNames = array_keys($this->pageDefinitions);
        $currentPosition = array_search($currentPage, $pageNames);

        if (array_key_exists($currentPosition + 1, $pageNames)) {
            return $pageNames[$currentPosition + 1];
        }
    }

    /**
     * @inheritdoc
     */
    public function hasPages()
    {
        return count($this->pageDefinitions) > 0;
    }
}
