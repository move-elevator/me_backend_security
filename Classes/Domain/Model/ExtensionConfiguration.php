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
    protected $specialChar;

    /**
     * @var int
     */
    protected $digit;

    /**
     * @var int
     */
    protected $capitalChar;

    /**
     * @var int
     */
    protected $lowercaseChar;

    /**
     * @var int
     */
    protected $passwordLength;

    /**
     * @var int
     */
    protected $validUntil;

    /**
     * @return int
     */
    public function getSpecialChar()
    {
        return $this->specialChar;
    }

    /**
     * @param int $specialChar
     */
    public function setSpecialChar($specialChar)
    {
        $this->specialChar = $specialChar;
    }

    /**
     * @return int
     */
    public function getDigit()
    {
        return $this->digit;
    }

    /**
     * @param int $digit
     */
    public function setDigit($digit)
    {
        $this->digit = $digit;
    }

    /**
     * @return int
     */
    public function getCapitalChar()
    {
        return $this->capitalChar;
    }

    /**
     * @param int $capitalChar
     */
    public function setCapitalChar($capitalChar)
    {
        $this->capitalChar = $capitalChar;
    }

    /**
     * @return int
     */
    public function getLowercaseChar()
    {
        return $this->lowercaseChar;
    }

    /**
     * @param int $lowercaseChar
     */
    public function setLowercaseChar($lowercaseChar)
    {
        $this->lowercaseChar = $lowercaseChar;
    }

    /**
     * @return int
     */
    public function getPasswordLength()
    {
        return $this->passwordLength;
    }

    /**
     * @param int $passwordLength
     */
    public function setPasswordLength($passwordLength)
    {
        $this->passwordLength = $passwordLength;
    }

    /**
     * @return int
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }

    /**
     * @param int $validUntil
     */
    public function setValidUntil($validUntil)
    {
        $this->validUntil = $validUntil;
    }
}
