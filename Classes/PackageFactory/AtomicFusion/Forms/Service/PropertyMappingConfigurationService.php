<?php
namespace PackageFactory\AtomicFusion\Forms\Service;

use TYPO3\Flow\Annotations as Flow;
use PackageFactory\AtomicFusion\Forms\Service\CryptographyService;
use TYPO3\Flow\Property\PropertyMappingConfigurationInterface;
use TYPO3\Flow\Property\TypeConverter\PersistentObjectConverter;

/**
 * @Flow\Scope("singleton")
 */
class PropertyMappingConfigurationService
{
	/**
	 * @Flow\Inject
	 * @var CryptographyService
	 */
	protected $cryptographyService;

	public function generateTrustedPropertiesToken(array $propertyNames)
	{
		return $this->cryptographyService->encodeHiddenFormMetadata($propertyNames);
	}

	public function applyTrustedPropertiesConfiguration(
		$trustedPropertyToken,
		PropertyMappingConfigurationInterface $propertyMappingConfiguration
	)
	{
		$propertyNames = $this->cryptographyService->decodeHiddenFormMetadata($trustedPropertyToken);

		foreach ($propertyNames as $propertyName) {
			$parts = explode('.', $propertyName);
			$currentPropertyMappingConfiguration = $propertyMappingConfiguration;

			foreach ($parts as $part) {
				$currentPropertyMappingConfiguration->allowProperties($part);
				$currentPropertyMappingConfiguration = $currentPropertyMappingConfiguration->forProperty($part);

				$currentPropertyMappingConfiguration->setTypeConverterOption(
					PersistentObjectConverter::class,
					PersistentObjectConverter::CONFIGURATION_MODIFICATION_ALLOWED,
					true
				);
				$currentPropertyMappingConfiguration->setTypeConverterOption(
					PersistentObjectConverter::class,
					PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
					true
				);
			}
		}

		return $propertyMappingConfiguration;
	}
}
