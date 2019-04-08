<?php

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
class SamePasswordValidator extends AbstractValidator
{
    const ERROR_CODE = 1513850698;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return void
     */
    protected function isValid($passwordChangeRequest): void
    {
        if (empty($passwordChangeRequest->getCurrentPassword())) {
            return;
        }

        if ($passwordChangeRequest->getCurrentPassword() !== $passwordChangeRequest->getPassword()) {
            return;
        }

        $this->addTranslatedError(
            self::ERROR_CODE
        );
    }
}
