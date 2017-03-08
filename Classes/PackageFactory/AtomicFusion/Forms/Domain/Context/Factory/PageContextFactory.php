<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Context\Factory;

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
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Context\PageContext;

/**
 * Creates page contexts
 *
 * @Flow\Scope("singleton")
 */
class PageContextFactory
{
    /**
     * Create a page context
     *
     * @param FormRuntimeInterface $formRuntime
     * @param string $pageName
     * @return PageContext
     */
    public function createPageContext(FormRuntimeInterface $formRuntime, $pageName)
    {
        return new PageContext($formRuntime, $pageName);
    }
}
