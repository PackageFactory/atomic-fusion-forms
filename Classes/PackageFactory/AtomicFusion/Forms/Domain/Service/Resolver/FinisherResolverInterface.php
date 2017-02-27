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

use Neos\Flow\Annotations as Flow;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FinisherDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Finishers\FinisherInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException;

/**
 * Method definitions for finisher resolver
 */
interface FinisherResolverInterface
{
    /**
     * Resolve a finisher definition to the finisher it describes
     *
     * @param FinisherDefinitionInterface $processorDefinition
     * @return FinisherInterface
     * @throws ResolverException when the implementation cannot be found or does not implement FinisherInterface
     */
    public function resolve(FinisherDefinitionInterface $finisherDefinition);
}
