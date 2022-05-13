<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Factory;

use InvalidArgumentException;
use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;

class ExtensionConfigurationFactory
{
    public static function create(array $rawExtensionConfiguration): ExtensionConfiguration
    {
        if (isset($rawExtensionConfiguration['minimumSpecialCharacters']) === false ||
            isset($rawExtensionConfiguration['minimumDigits']) === false ||
            isset($rawExtensionConfiguration['minimumCapitalCharacters']) === false ||
            isset($rawExtensionConfiguration['minimumLowercaseCharacters']) === false ||
            isset($rawExtensionConfiguration['passwordLength']) === false ||
            isset($rawExtensionConfiguration['maximumValidDays']) === false
        ) {
            throw new InvalidArgumentException(
                'Some parameters are missing in given extension configuration.',
                1512479995
            );
        }

        return self::createExtensionConfiguration($rawExtensionConfiguration);
    }

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
