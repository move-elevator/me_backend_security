<?php

namespace MoveElevator\MeBackendSecurity\Factory;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;

/**
 * @package MoveElevator\MeBackendSecurity\Factory
 */
class ExtensionConfigurationFactory
{
    /**
     * @param $rawExtensionConfiguration
     *
     * @return ExtensionConfiguration
     */
    public static function create($rawExtensionConfiguration)
    {
        if (empty($rawExtensionConfiguration['specialChar']['value']) ||
            empty($rawExtensionConfiguration['digit']['value']) ||
            empty($rawExtensionConfiguration['capitalChar']['value']) ||
            empty($rawExtensionConfiguration['lowercaseChar']['value']) ||
            empty($rawExtensionConfiguration['passwordLength']['value']) ||
            empty($rawExtensionConfiguration['validUntil']['value'])
        ) {
            throw new \InvalidArgumentException(
                'The given arguments are incomplete!'
            );
        }

        $extensionConfiguration = new ExtensionConfiguration();
        $extensionConfiguration->setSpecialChar((int)$rawExtensionConfiguration['specialChar']['value']);
        $extensionConfiguration->setDigit((int)$rawExtensionConfiguration['digit']['value']);
        $extensionConfiguration->setCapitalChar((int)$rawExtensionConfiguration['capitalChar']['value']);
        $extensionConfiguration->setLowercaseChar((int)$rawExtensionConfiguration['lowercaseChar']['value']);
        $extensionConfiguration->setPasswordLength((int)$rawExtensionConfiguration['passwordLength']['value']);
        $extensionConfiguration->setValidUntil((int)$rawExtensionConfiguration['validUntil']['value']);

        return $extensionConfiguration;
    }
}
