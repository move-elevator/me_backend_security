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
    protected $minimalSpecialCharacters;

    /**
     * @var int
     */
    protected $minimalDigits;

    /**
     * @var int
     */
    protected $minimalCapitalCharacters;

    /**
     * @var int
     */
    protected $minimalLowercaseCharacters;

    /**
     * @var int
     */
    protected $passwordLength;

    /**
     * @var int
     */
    protected $maximumValidDays;

    /**
     * @param int $minimalSpecialCharacters
     * @param int $minimalDigits
     * @param int $minimalCapitalCharacters
     * @param int $minimalLowercaseCharacters
     * @param int $passwordLength
     * @param int $maximumValidDays
     */
    public function __construct(
        $minimalSpecialCharacters,
        $minimalDigits,
        $minimalCapitalCharacters,
        $minimalLowercaseCharacters,
        $passwordLength,
        $maximumValidDays
    ) {
        $this->minimalSpecialCharacters = $minimalSpecialCharacters;
        $this->minimalDigits = $minimalDigits;
        $this->minimalCapitalCharacters = $minimalCapitalCharacters;
        $this->minimalLowercaseCharacters = $minimalLowercaseCharacters;
        $this->passwordLength = $passwordLength;
        $this->maximumValidDays = $maximumValidDays;
    }

    /**
     * @return int
     */
    public function getMinimalSpecialCharacters()
    {
        return $this->minimalSpecialCharacters;
    }

    /**
     * @return int
     */
    public function getMinimalDigits()
    {
        return $this->minimalDigits;
    }

    /**
     * @return int
     */
    public function getMinimalCapitalCharacters()
    {
        return $this->minimalCapitalCharacters;
    }

    /**
     * @return int
     */
    public function getMinimalLowercaseCharacters()
    {
        return $this->minimalLowercaseCharacters;
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
