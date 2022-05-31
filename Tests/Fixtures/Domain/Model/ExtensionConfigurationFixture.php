<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;

trait ExtensionConfigurationFixture
{
    protected int $minimumSpecialCharacters = 1;
    protected int $minimumDigits = 1;
    protected int $minimumCapitalCharacters = 1;
    protected int $minimumLowercaseCharacters = 1;
    protected int $passwordLength = 1;
    protected int $maximumValidDays = 14;

    protected function getExtensionConfigurationFixture(): ExtensionConfiguration
    {
        return new ExtensionConfiguration(
            $this->minimumSpecialCharacters,
            $this->minimumDigits,
            $this->minimumCapitalCharacters,
            $this->minimumLowercaseCharacters,
            $this->passwordLength,
            $this->maximumValidDays
        );
    }

    protected function getRawExtensionConfigurationFixture(): array
    {
        return [
            'minimumSpecialCharacters' => $this->minimumSpecialCharacters,
            'minimumDigits' => $this->minimumDigits,
            'minimumCapitalCharacters' => $this->minimumCapitalCharacters,
            'minimumLowercaseCharacters' => $this->minimumLowercaseCharacters,
            'passwordLength' => $this->passwordLength,
            'maximumValidDays' => $this->maximumValidDays
        ];
    }
}
