<?php

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
class PasswordLengthValidator extends AbstractValidator
{
    const ERROR_CODE = 1510742741;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return void
     */
    protected function isValid($passwordChangeRequest): void
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->options['extensionConfiguration'];

        if (strlen($passwordChangeRequest->getPassword()) >= $configuration->getPasswordLength()) {
            return;
        }

        $this->addTranslatedError(
            self::ERROR_CODE,
            $configuration->getPasswordLength()
        );
    }
}
