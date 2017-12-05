<?php

namespace MoveElevator\MeBackendSecurity\Domain\Model;

/**
 * @package MoveElevator\MeBackendSecurity\Domain\Model
 */
class ExtensionConfiguration
{
    /**
     * @var int
     */
    protected $minimumSpecialCharacters;

    /**
     * @var int
     */
    protected $minimumDigits;

    /**
     * @var int
     */
    protected $minimumCapitalCharacters;

    /**
     * @var int
     */
    protected $minimumLowercaseCharacters;

    /**
     * @var int
     */
    protected $passwordLength;

    /**
     * @var int
     */
    protected $maximumValidDays;

    /**
     * @param int $minimumSpecialCharacters
     * @param int $minimumDigits
     * @param int $minimumCapitalCharacters
     * @param int $minimumLowercaseCharacters
     * @param int $passwordLength
     * @param int $maximumValidDays
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $minimumSpecialCharacters,
        $minimumDigits,
        $minimumCapitalCharacters,
        $minimumLowercaseCharacters,
        $passwordLength,
        $maximumValidDays
    ) {
        $this->minimumSpecialCharacters = $minimumSpecialCharacters;
        $this->minimumDigits = $minimumDigits;
        $this->minimumCapitalCharacters = $minimumCapitalCharacters;
        $this->minimumLowercaseCharacters = $minimumLowercaseCharacters;
        $this->passwordLength = $passwordLength;
        $this->maximumValidDays = $maximumValidDays;
    }

    /**
     * @return int
     */
    public function getMinimumSpecialCharacters()
    {
        return $this->minimumSpecialCharacters;
    }

    /**
     * @return int
     */
    public function getMinimumDigits()
    {
        return $this->minimumDigits;
    }

    /**
     * @return int
     */
    public function getMinimumCapitalCharacters()
    {
        return $this->minimumCapitalCharacters;
    }

    /**
     * @return int
     */
    public function getMinimumLowercaseCharacters()
    {
        return $this->minimumLowercaseCharacters;
    }

    /**
     * @return int
     */
    public function getPasswordLength()
    {
        return $this->passwordLength;
    }

    /**
     * @return int
     */
    public function getMaximumValidDays()
    {
        return $this->maximumValidDays;
    }
}
