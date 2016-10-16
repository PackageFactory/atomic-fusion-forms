<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver;

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
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ProcessorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Processors\ProcessorInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException;

/**
 * Method definitions for processor resolver
 */
interface ProcessorResolverInterface
{
    /**
     * Resolve a processor definition to the processor it describes
     *
     * @param ProcessorDefinitionInterface $processorDefinition
     * @return ProcessorInterface
     * @throws ResolverException when the implementation cannot be found or does not implement ProcessorInterface
     */
    public function resolve(ProcessorDefinitionInterface $processorDefinition);
}
