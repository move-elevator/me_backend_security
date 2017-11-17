<?php

namespace MoveElevator\MeBackendSecurity\Factory;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;

/**
 * @package MoveElevator\MeBackendSecurity\Factory
 */
class ExtensionConfigurationFactory
{
    /**
     * @param array $rawExtensionConfiguration
     *
     * @return ExtensionConfiguration
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public static function create($rawExtensionConfiguration)
    {
        if (isset($rawExtensionConfiguration['minimalSpecialCharacters']['value']) === false ||
            isset($rawExtensionConfiguration['minimalDigits']['value']) === false ||
            isset($rawExtensionConfiguration['minimalCapitalCharacters']['value']) === false ||
            isset($rawExtensionConfiguration['minimalLowercaseCharacters']['value']) === false ||
            isset($rawExtensionConfiguration['passwordLength']['value']) === false ||
            isset($rawExtensionConfiguration['maximumValidDays']['value']) === false
        ) {
            throw new \InvalidArgumentException(
                'The given arguments are incomplete!'
            );
        }

        return self::createExtensionConfiguration($rawExtensionConfiguration);
    }

    /**
     * @param array $rawExtensionConfiguration
     *
     * @return ExtensionConfiguration
     */
    private static function createExtensionConfiguration($rawExtensionConfiguration)
    {
        $extensionConfiguration = new ExtensionConfiguration(
            (int) $rawExtensionConfiguration['minimalSpecialCharacters']['value'],
            (int) $rawExtensionConfiguration['minimalDigits']['value'],
            (int) $rawExtensionConfiguration['minimalCapitalCharacters']['value'],
            (int) $rawExtensionConfiguration['minimalLowercaseCharacters']['value'],
            (int) $rawExtensionConfiguration['passwordLength']['value'],
            (int) $rawExtensionConfiguration['maximumValidDays']['value']
        );

        return $extensionConfiguration;
    }
}
