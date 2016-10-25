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

use TYPO3\Flow\Annotations as Flow;
use TYPO3\Eel\ProtectedContextAwareInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;

/**
 * Form context for use in fusion
 */
class FormContext implements ProtectedContextAwareInterface
{
	/**
	 * @var FormRuntimeInterface
	 */
	protected $formRuntime;

    /**
     * @Flow\Inject
     * @var Factory\FieldContextFactory
     */
    protected $fieldContextFactory;

    /**
     * @Flow\Inject
     * @var Factory\PageContextFactory
     */
    protected $pageContextFactory;

	/**
	 * Constructor
	 *
	 * @param FormRuntimeInterface $formRuntime
	 */
	public function __construct(FormRuntimeInterface $formRuntime)
	{
		$this->formRuntime = $formRuntime;
	}

	/**
	 * Get the form label
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->formRuntime->getFormDefinition()->getLabel();
	}

	/**
	 * Get the form name
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->formRuntime->getFormDefinition()->getName();
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
	 * Create a page context for the given page name
	 *
	 * @param string $pageName
	 * @return PageContext
	 */
	public function page($pageName)
	{
		return $this->pageContextFactory->createPageContext($this->formRuntime, $pageName);
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
