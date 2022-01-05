<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

class SamePasswordValidator extends AbstractValidator
{
    private const ERROR_CODE = 1513850698;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
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
