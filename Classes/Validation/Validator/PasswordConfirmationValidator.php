<?php

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
class PasswordConfirmationValidator extends AbstractValidator
{
    const ERROR_CODE = 1510742742;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return void
     */
    protected function isValid($passwordChangeRequest): void
    {
        if ($passwordChangeRequest->getPassword() === $passwordChangeRequest->getPasswordConfirmation()) {
            return;
        }

        $this->addTranslatedError(
            self::ERROR_CODE
        );
    }
}
