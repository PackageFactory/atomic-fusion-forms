<?php
namespace PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\Task;

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
use Neos\Error\Messages\Result;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\ValidatorResolverInterface;
use PackageFactory\AtomicFusion\Forms\Factory\MessageFactory;

/**
 * Validate form values
 *
 * @Flow\Scope("singleton")
 */
class ValidateTask implements ValidateTaskInterface
{
    /**
     * @Flow\Inject
     * @var ValidatorResolverInterface
     */
    protected $validatorResolver;

    /**
	 * @Flow\Inject
	 * @var MessageFactory
	 */
	protected $messageFactory;

    /**
     * @inheritdoc
     */
    public function run(FieldDefinitionInterface $fieldDefinition, $value, Result $validationResult)
    {
        foreach ($fieldDefinition->getValidatorDefinitions() as $validatorDefinition) {
            $validator = $this->validatorResolver->resolve($validatorDefinition);
            $singleValidationResult = $validator->validate($value);

            if ($singleValidationResult->hasErrors() && $validatorDefinition->hasCustomErrorMessage()) {
                $customErrorMessage = $this->messageFactory
                    ->createError($validatorDefinition->getCustomErrorMessage());

                $validationResult->forProperty($fieldDefinition->getName())->addError($customErrorMessage);
                continue;
            }

            $validationResult->forProperty($fieldDefinition->getName())->merge($singleValidationResult);
        }
    }
}
