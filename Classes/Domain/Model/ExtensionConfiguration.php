<?php
declare(strict_types=1);

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
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
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

    /**
     * @return int
     */
    public function getMinimumSpecialCharacters(): int
    {
        return $this->minimumSpecialCharacters;
    }

    /**
     * @return int
     */
    public function getMinimumDigits(): int
    {
        return $this->minimumDigits;
    }

    /**
     * @return int
     */
    public function getMinimumCapitalCharacters(): int
    {
        return $this->minimumCapitalCharacters;
    }

    /**
     * @return int
     */
    public function getMinimumLowercaseCharacters(): int
    {
        return $this->minimumLowercaseCharacters;
    }

    /**
     * @return int
     */
    public function getPasswordLength(): int
    {
        return $this->passwordLength;
    }

    /**
     * @return int
     */
    public function getMaximumValidDays(): int
    {
        return $this->maximumValidDays;
    }
}
