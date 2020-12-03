<?php
declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
final class PasswordLengthValidator extends AbstractValidator
{
    protected const ERROR_CODE = 1510742741;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     */
    protected function isValid($passwordChangeRequest): void
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->options['extensionConfiguration'];

        if (mb_strlen($passwordChangeRequest->getPassword()) >= $configuration->getPasswordLength()) {
            return;
        }

        $this->addTranslatedError(
            self::ERROR_CODE,
            $configuration->getPasswordLength()
        );
    }
}
