<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

class PasswordConfirmationValidator extends AbstractValidator
{
    private const ERROR_CODE = 1510742742;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
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
