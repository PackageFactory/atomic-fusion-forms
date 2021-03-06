<?php
namespace PackageFactory\AtomicFusion\Forms\Factory;

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
use Neos\Flow\Property\PropertyMappingConfiguration;
use PackageFactory\AtomicFusion\Forms\Service\PropertyMappingConfigurationService;

/**
 * @Flow\Scope("singleton")
 */
class PropertyMappingConfigurationFactory
{
    /**
     * @Flow\Inject
     * @var PropertyMappingConfigurationService
     */
    protected $propertyMappingConfigurationService;

    /**
     * Create a new property mapping configuration
     *
     * @return PropertyMappingConfiguration
     */
    public function createPropertyMappingConfiguration()
    {
        return new PropertyMappingConfiguration();
    }

    /**
     * Create a new property mapping configuration with pre-configuered trusted properties
     *
     * @param string $trustedPropertyToken
     * @return PropertyMappingConfiguration
     */
    public function createTrustedPropertyMappingConfiguration($trustedPropertyToken)
    {
        if ($trustedPropertyToken !== null && is_string($trustedPropertyToken)) {
            return $this->propertyMappingConfigurationService->applyTrustedPropertiesConfiguration(
                $trustedPropertyToken,
                $this->createPropertyMappingConfiguration()
            );
        }

        return $this->createPropertyMappingConfiguration();
    }
}
