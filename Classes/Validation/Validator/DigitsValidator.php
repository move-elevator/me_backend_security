<?php

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
class DigitsValidator extends AbstractValidator
{
    const PATTERN_DIGIT = '/[0-9]/';
    const ERROR_CODE = 1510742744;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return void
     */
    protected function isValid($passwordChangeRequest)
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->options['extensionConfiguration'];

        $matches = preg_match_all(self::PATTERN_DIGIT, $passwordChangeRequest->getPassword());

        if ($matches >= $configuration->getMinimumDigits()) {
            return;
        }

        $singular = false;

        if ($configuration->getMinimumDigits() === 1) {
            $singular = true;
        }

        $this->addTranslatedError(
            self::ERROR_CODE,
            $configuration->getMinimumDigits(),
            $singular
        );
    }
}
