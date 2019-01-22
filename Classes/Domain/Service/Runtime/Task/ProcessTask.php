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
use Neos\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\FieldDefinitionInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Resolver\ProcessorResolverInterface;
use PackageFactory\AtomicFusion\Forms\Domain\Service\Runtime\FormRuntimeInterface;

/**
 * Process request arguments
 *
 * @Flow\Scope("singleton")
 */
class ProcessTask implements TaskInterface
{
    /**
     * @Flow\Inject
     * @var ProcessorResolverInterface
     */
    protected $processorResolver;

    /**
     * @inheritdoc
     */
    public function shouldRun(FormRuntimeInterface $runtime)
    {
        return !$runtime->getFormState()->isInitialCall();
    }

    /**
     * @inheritdoc
     */
    public function run(FormRuntimeInterface $runtime)
    {
        $fieldDefinitions = $runtime->getFieldDefinitionsForCurrentPage();
        $propertyMappingConfiguration = $runtime->getPropertyMappingConfiguration();

        $this->values = [];
        foreach ($fieldDefinitions as $fieldDefinition) {
            $argument = $runtime->getFormState()->getArgument($fieldDefinition->getName());

            $value = $this->process(
                $propertyMappingConfiguration,
                $fieldDefinition,
                $argument,
                $runtime->getFormState()->getValidationResult()
            );

            $runtime->getFormState()->addValue($fieldDefinition->getName(), $value);
        }
    }

    /**
     * Process the given arguments by their field definitions and write possibly occuring messages
     * to the given validation result
     *
     * @param PropertyMappingConfiguration $propertyMappingConfiguration
     * @param FieldDefinitionInterface $fieldDefinition
     * @param mixed $input
     * @param Result $validationResult
     * @return array The processed arguments
     */
    protected function process(
        PropertyMappingConfiguration $propertyMappingConfiguration,
        FieldDefinitionInterface $fieldDefinition,
        $input,
        Result $validationResult
    ) {
    
        $processor = $this->processorResolver->resolve($fieldDefinition->getProcessorDefinition());

        return $processor->apply(
            $propertyMappingConfiguration->forProperty($fieldDefinition->getName()),
            $validationResult->forProperty($fieldDefinition->getName()),
            $fieldDefinition,
            $fieldDefinition->getProcessorDefinition()->getOptions(),
            $input
        );
    }
}
