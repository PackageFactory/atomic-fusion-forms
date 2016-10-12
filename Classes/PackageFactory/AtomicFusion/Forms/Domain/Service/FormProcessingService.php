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
use TYPO3\Flow\Error\Result;
use TYPO3\Flow\Error\Error;
use TYPO3\Flow\Utility\Arrays;
use TYPO3\Flow\Property\PropertyMappingConfiguration;
use TYPO3\Flow\Property\PropertyMapper;
use TYPO3\Flow\Validation\Validator\ConjunctionValidator;
use TYPO3\Flow\Validation\ValidatorResolver;
use TYPO3\TypoScript\Core\Runtime;
use PackageFactory\AtomicFusion\Forms\Service\PropertyMappingConfigurationService;

/**
 * @Flow\Scope("singleton")
 */
class FormProcessingService
{
	/**
     * @Flow\Inject
     * @var PropertyMapper
     */
    protected $propertyMapper;

	/**
     * @Flow\Inject
     * @var ValidatorResolver
     */
    protected $validatorResolver;

	/**
	 * @Flow\Inject
	 * @var PropertyMappingConfigurationService
	 */
	protected $propertyMappingConfigurationService;

	public function process(FormContext $formContext)
	{
		$validationResult =  new Result();
		$fieldConfiguration = $formContext->getFieldConfiguration();
		$globalPropertyMappingConfiguration = $this->propertyMappingConfigurationService
			->applyTrustedPropertiesConfiguration(
				$formContext->getRequest()->getInternalArgument('__trustedProperties'),
				new PropertyMappingConfiguration()
			);

		foreach ($fieldConfiguration as $fieldName => $configuration) {
			if (isset($configuration['page']) && !$formContext->getFormState()->isCurrentPage($configuration['page'])) {
				continue;
			}
			$propertyMappingConfiguration = $globalPropertyMappingConfiguration->forProperty($fieldName);
			$conjunctionValidator = new ConjunctionValidator();
			$value = $formContext->getFieldValueForPath($fieldName);

			if ($type = Arrays::getValueByPath($configuration, 'type')) {
				$value = $this->propertyMapper->convert($value, $type, $propertyMappingConfiguration);
	            $validationResult->forProperty($fieldName)->merge($this->propertyMapper->getMessages());
			}


			foreach ($configuration['validators'] as $validator) {
				$conjunctionValidator->addValidator($validator);
			}

			$validationResult->forProperty($fieldName)->merge($conjunctionValidator->validate($value));
			$formContext->setResult($fieldName, $value);
		}

		return $validationResult;
	}
}
