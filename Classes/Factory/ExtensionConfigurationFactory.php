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
        if (isset($rawExtensionConfiguration['specialChar']['value']) === false ||
            isset($rawExtensionConfiguration['digit']['value']) === false ||
            isset($rawExtensionConfiguration['capitalChar']['value']) === false ||
            isset($rawExtensionConfiguration['lowercaseChar']['value']) === false ||
            isset($rawExtensionConfiguration['passwordLength']['value']) === false ||
            isset($rawExtensionConfiguration['validUntil']['value']) === false
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
