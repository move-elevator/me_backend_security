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
    public static function create(array $rawExtensionConfiguration): ExtensionConfiguration
    {
        if (isset($rawExtensionConfiguration['minimumSpecialCharacters']) === false ||
            isset($rawExtensionConfiguration['minimumDigits']) === false ||
            isset($rawExtensionConfiguration['minimumCapitalCharacters']) === false ||
            isset($rawExtensionConfiguration['minimumLowercaseCharacters']) === false ||
            isset($rawExtensionConfiguration['passwordLength']) === false ||
            isset($rawExtensionConfiguration['maximumValidDays']) === false
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
    private static function createExtensionConfiguration(array $rawExtensionConfiguration): ExtensionConfiguration
    {
        $extensionConfiguration = new ExtensionConfiguration(
            (int) $rawExtensionConfiguration['minimumSpecialCharacters'],
            (int) $rawExtensionConfiguration['minimumDigits'],
            (int) $rawExtensionConfiguration['minimumCapitalCharacters'],
            (int) $rawExtensionConfiguration['minimumLowercaseCharacters'],
            (int) $rawExtensionConfiguration['passwordLength'],
            (int) $rawExtensionConfiguration['maximumValidDays']
        );

        return $extensionConfiguration;
    }
}
