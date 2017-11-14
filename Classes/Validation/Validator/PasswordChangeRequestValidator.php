<?php

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
class PasswordChangeRequestValidator extends AbstractValidator
{
    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return void
     */
    protected function isValid($passwordChangeRequest)
    {
        $this->addError(
            $this->translateErrorMessage(
                'form.input.validation.error.boolean.mandatory',
                'me_backend_security'
            ),
            100
        );
    }
}
