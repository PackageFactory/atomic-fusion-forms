<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Context;

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
use Neos\Eel\ProtectedContextAwareInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;

/**
 * Page context for use in fusion
 */
class PageContext implements ProtectedContextAwareInterface
{
    /**
     * @var FormRuntimeInterface
     */
    protected $formRuntime;

    /**
     * @var string
     */
    protected $pageName;

    /**
     * @Flow\Inject
     * @var Factory\FieldContextFactory
     */
    protected $fieldContextFactory;

    /**
     * Constructor
     *
     * @param FormRuntimeInterface $formRuntime
     * @param string $pageName
     */
    public function __construct(FormRuntimeInterface $formRuntime, $pageName)
    {
        $this->formRuntime = $formRuntime;
        $this->pageName = $pageName;
    }

    /**
     * Get the page label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->formRuntime->getFormDefinition()->getPageDefinition($this->pageName)->getLabel();
    }

    /**
     * Get the page name
     *
     * @return string
     */
    public function getName()
    {
        return $this->formRuntime->getFormDefinition()->getPageDefinition($this->pageName)->getName();
    }

    /**
     * Create a field context for the given path
     *
     * @param string $path
     * @return FieldContext
     */
    public function field($path)
    {
        $pathParts = explode('.', $path);
        $name = array_shift($pathParts);

        return $this->fieldContextFactory->createFieldContext($this->formRuntime, $name, implode('.', $pathParts));
    }

    /**
     * Check if this represents the current page
     *
     * @return boolean
     */
    public function isCurrentPage()
    {
        return $this->formRuntime->getFormState()->isCurrentPage($this->pageName);
    }

    /**
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName)
    {
        return true;
    }
}
