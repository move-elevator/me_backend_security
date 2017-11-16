<?php

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
class PasswordChangeRequestValidator extends AbstractValidator
{
    const PATTERN_SPECIALCHAR = '/[!$%&\/=?,.]/';
    const PATTERN_DIGIT = '/[0-9]/';
    const PATTERN_CAPITALCHAR = '/[A-ZÄÖÜ]/';
    const PATTERN_LOWERCASECHAR = '/[a-zäöü]/';

    /**
     * @var array
     */
    protected $supportedOptions = [
        'extensionConfiguration' => [0, 'The extension configuration object', ExtensionConfiguration::class, true]
    ];

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return void
     */
    protected function isValid($passwordChangeRequest)
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->options['extensionConfiguration'];

        if ($this->validatePasswordLength($passwordChangeRequest, $configuration->getPasswordLength()) === false) {
            $this->addError(
                $this->translateErrorMessage(
                    'error.1510742747',
                    'me_backend_security'
                ),
                1510742741
            );
        }

        if ($this->validatePasswordConfirmation($passwordChangeRequest) === false) {
            $this->addError(
                $this->translateErrorMessage(
                    'error.1510742742',
                    'me_backend_security'
                ),
                1510742742
            );
        }

        if ($this->validateSpecialChar($passwordChangeRequest, $configuration->getSpecialChar()) === false) {
            $this->addError(
                $this->translateErrorMessage(
                    'error.1510742743',
                    'me_backend_security'
                ),
                1510742743
            );
        }

        if ($this->validateDigit($passwordChangeRequest, $configuration->getDigit()) === false) {
            $this->addError(
                $this->translateErrorMessage(
                    'error.1510742744',
                    'me_backend_security'
                ),
                1510742744
            );
        }

        if ($this->validateCapitalChar($passwordChangeRequest, $configuration->getCapitalChar()) === false) {
            $this->addError(
                $this->translateErrorMessage(
                    'error.1510742745',
                    'me_backend_security'
                ),
                1510742745
            );
        }

        if ($this->validateLowerCaseChar($passwordChangeRequest, $configuration->getLowercaseChar()) === false) {
            $this->addError(
                $this->translateErrorMessage(
                    'error.1510742746',
                    'me_backend_security'
                ),
                1510742746
            );
        }
    }

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     * @param int                   $minimum
     *
     * @return bool
     */
    protected function validatePasswordLength($passwordChangeRequest, $minimum)
    {
        if (strlen($passwordChangeRequest->getPassword()) >= $minimum) {
            return true;
        }

        return false;
    }

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return bool
     */
    protected function validatePasswordConfirmation($passwordChangeRequest)
    {
        if ($passwordChangeRequest->getPassword() === $passwordChangeRequest->getPasswordConfirmation()) {
            return true;
        }

        return false;
    }

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     * @param int                   $minimum
     *
     * @return bool
     */
    protected function validateSpecialChar($passwordChangeRequest, $minimum)
    {
        $matches = preg_match_all(self::PATTERN_SPECIALCHAR, $passwordChangeRequest->getPassword());

        if ($matches >= $minimum) {
            return true;
        }

        return false;
    }

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     * @param int                   $minimum
     *
     * @return bool
     */
    protected function validateDigit($passwordChangeRequest, $minimum)
    {
        $matches = preg_match_all(self::PATTERN_DIGIT, $passwordChangeRequest->getPassword());

        if ($matches >= $minimum) {
            return true;
        }

        return false;
    }

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     * @param int                   $minimum
     *
     * @return bool
     */
    protected function validateCapitalChar($passwordChangeRequest, $minimum)
    {
        $matches = preg_match_all(self::PATTERN_CAPITALCHAR, $passwordChangeRequest->getPassword());

        if ($matches >= $minimum) {
            return true;
        }

        return false;
    }

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     * @param int                   $minimum
     *
     * @return bool
     */
    protected function validateLowerCaseChar($passwordChangeRequest, $minimum)
    {
        $matches = preg_match_all(self::PATTERN_LOWERCASECHAR, $passwordChangeRequest->getPassword());

        if ($matches >= $minimum) {
            return true;
        }

        return false;
    }
}
