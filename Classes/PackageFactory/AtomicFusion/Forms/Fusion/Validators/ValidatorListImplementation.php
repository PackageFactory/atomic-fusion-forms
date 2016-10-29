<?php
namespace PackageFactory\AtomicFusion\Forms\Fusion\Validators;

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
use TYPO3\TypoScript\TypoScriptObjects\AbstractArrayTypoScriptObject;
use PackageFactory\AtomicFusion\Forms\Domain\Model\Definition\ValidatorDefinitionInterface;

/**
 * Fusion object to create lists of validator definitions
 */
class ValidatorListImplementation extends AbstractArrayTypoScriptObject
{
    /**
     * Returns a list of validator definitions
     *
     * @return array<ValidatorDefinitionInterface>
     */
    public function evaluate()
    {
        $result = [];

        foreach ($this->properties as $key => $configuration) {
            $validatorDefinition = $this->renderValidatorDefinition($key, $configuration);

            $result[$validatorDefinition->getName()] = $validatorDefinition;
        }

        return $result;
    }

    /**
     * Render a single validator definition
     *
     * @param string $key
     * @param array $configuration
     * @return ValidatorDefinitionInterface
     */
    protected function renderValidatorDefinition($key, $configuration)
    {
        if (isset($configuration['__objectType'])) {
            return $this->tsRuntime->render(
                sprintf('%s/%s', $this->path, $key)
            );
        }

        return $this->tsRuntime->render(
            sprintf('%s/%s<PackageFactory.AtomicFusion.Forms:Validator>', $this->path, $key)
        );
    }
}
