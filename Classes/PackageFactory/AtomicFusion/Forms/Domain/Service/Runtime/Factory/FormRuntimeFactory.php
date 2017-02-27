<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Factory;

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
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntime;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;
use Neos\Flow\Mvc\ActionRequest;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FormDefinitionInterface;

/**
 * Create form definitions
 *
 * @Flow\Scope("singleton")
 * @codeCoverageIgnore
 */
class FormRuntimeFactory
{
    /**
     * Create a new Form runtime
     * @param FormDefinitionInterface $formDefinition
     * @param ActionRequest $request
     * @return FormRuntimeInterface
     */
    public function createFormRuntime(FormDefinitionInterface $formDefinition, ActionRequest $request)
    {
        return new FormRuntime($formDefinition, $request);
    }
}
