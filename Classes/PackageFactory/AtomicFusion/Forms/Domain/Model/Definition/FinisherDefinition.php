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
 * Runtime finisher definition
 */
class FinisherDefinition implements FinisherDefinitionInterface
{
    /**
     * @var string
     */
    protected $implementationClassName;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param string $implementationClassName
     * @param array $options
     */
    public function __construct($implementationClassName, array $options)
    {
        $this->implementationClassName = $implementationClassName;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function getImplementationClassName()
    {
        return $this->implementationClassName;
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->options;
    }
}
