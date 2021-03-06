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

/**
 *  Runtime validator definition
 */
class ValidatorDefinition implements ValidatorDefinitionInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $implementationClassName;

    /**
     * @var array
     */
    protected $options;

    /**
     * [$customErrorMessage description]
     * @var string
     */
    protected $customErrorMessage = '';

    /**
     * Constructor
     *
     * @param string $implementationClassName
     * @param array $options
     */
    public function __construct($name, $implementationClassName, array $options)
    {
        $this->name = $name;
        $this->implementationClassName = $implementationClassName;
        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
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

    /**
     * Set the custom error message
     *
     * @param string $message
     * @return void
     */
    public function setCustomErrorMessage($message)
    {
        $this->customErrorMessage = $message;
    }

    /**
     * @inheritdoc
     */
    public function getCustomErrorMessage()
    {
        return $this->customErrorMessage;
    }

    /**
     * @inheritdoc
     */
    public function hasCustomErrorMessage()
    {
        return !empty($this->customErrorMessage);
    }
}
