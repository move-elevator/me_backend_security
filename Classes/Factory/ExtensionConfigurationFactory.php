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
        if (isset($rawExtensionConfiguration['minimumSpecialCharacters']['value']) === false ||
            isset($rawExtensionConfiguration['minimumDigits']['value']) === false ||
            isset($rawExtensionConfiguration['minimumCapitalCharacters']['value']) === false ||
            isset($rawExtensionConfiguration['minimumLowercaseCharacters']['value']) === false ||
            isset($rawExtensionConfiguration['passwordLength']['value']) === false ||
            isset($rawExtensionConfiguration['maximumValidDays']['value']) === false
        ) {
            throw new \InvalidArgumentException(
                'Some parameters are missing in given extension configuration.',
                1512479995
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
            (int) $rawExtensionConfiguration['minimumSpecialCharacters']['value'],
            (int) $rawExtensionConfiguration['minimumDigits']['value'],
            (int) $rawExtensionConfiguration['minimumCapitalCharacters']['value'],
            (int) $rawExtensionConfiguration['minimumLowercaseCharacters']['value'],
            (int) $rawExtensionConfiguration['passwordLength']['value'],
            (int) $rawExtensionConfiguration['maximumValidDays']['value']
        );

        return $extensionConfiguration;
    }
}
