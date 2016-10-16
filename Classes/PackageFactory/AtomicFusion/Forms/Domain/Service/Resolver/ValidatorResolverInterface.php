<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service;

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
use TYPO3\Flow\Validation\Validators\ValidatorInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Exception\ResolverException;

/**
 * Method definitions for validator resolver
 */
interface ValidatorResolverInterface
{
    /**
     * Resolve a validator definition to the validator it describes
     *
     * @param ValidatorDefinitionInterface $processorDefinition
     * @return ValidatorInterface
     * @throws ResolverException
     */
    public function resolve(ValidatorDefinitionInterface $validatorDefinition);
}
