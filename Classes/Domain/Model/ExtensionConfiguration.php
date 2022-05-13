<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Domain\Model;

class ExtensionConfiguration
{
    protected int $minimumSpecialCharacters;
    protected int $minimumDigits;
    protected int $minimumCapitalCharacters;
    protected int $minimumLowercaseCharacters;
    protected int $passwordLength;
    protected int $maximumValidDays;

    public function __construct(
        int $minimumSpecialCharacters,
        int $minimumDigits,
        int $minimumCapitalCharacters,
        int $minimumLowercaseCharacters,
        int $passwordLength,
        int $maximumValidDays
    ) {
        $this->minimumSpecialCharacters = $minimumSpecialCharacters;
        $this->minimumDigits = $minimumDigits;
        $this->minimumCapitalCharacters = $minimumCapitalCharacters;
        $this->minimumLowercaseCharacters = $minimumLowercaseCharacters;
        $this->passwordLength = $passwordLength;
        $this->maximumValidDays = $maximumValidDays;
    }

    public function getMinimumSpecialCharacters(): int
    {
        return $this->minimumSpecialCharacters;
    }

    public function getMinimumDigits(): int
    {
        return $this->minimumDigits;
    }

    public function getMinimumCapitalCharacters(): int
    {
        return $this->minimumCapitalCharacters;
    }

    public function getMinimumLowercaseCharacters(): int
    {
        return $this->minimumLowercaseCharacters;
    }

    public function getPasswordLength(): int
    {
        return $this->passwordLength;
    }

    public function getMaximumValidDays(): int
    {
        return $this->maximumValidDays;
    }
}
