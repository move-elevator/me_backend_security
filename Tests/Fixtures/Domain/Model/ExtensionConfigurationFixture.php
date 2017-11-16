<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Fixture\Domain\Model
 */
trait ExtensionConfigurationFixture
{
    /**
     * @var int
     */
    protected $specialChar = 1;

    /**
     * @var int
     */
    protected $digit = 1;

    /**
     * @var int
     */
    protected $capitalChar = 1;

    /**
     * @var int
     */
    protected $lowercaseChar = 1;

    /**
     * @var int
     */
    protected $passwordLength = 8;

    /**
     * @var int
     */
    protected $validUntil = 14;

    /**
     * @return ExtensionConfiguration
     */
    protected function getAddressFixtureObject()
    {
        $extensionConfiguration = new ExtensionConfiguration();
        $extensionConfiguration->setSpecialChar($this->specialChar);
        $extensionConfiguration->setDigit($this->digit);
        $extensionConfiguration->setCapitalChar($this->capitalChar);
        $extensionConfiguration->setLowercaseChar($this->lowercaseChar);
        $extensionConfiguration->setPasswordLength($this->passwordLength);
        $extensionConfiguration->setValidUntil($this->validUntil);

        return $extensionConfiguration;
    }
}
