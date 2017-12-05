<?php

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
class CapitalCharactersValidator extends AbstractValidator
{
    const PATTERN_CAPITALCHAR = '/[A-ZÄÖÜ]/';
    const ERROR_CODE = 1510742745;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return void
     */
    protected function isValid($passwordChangeRequest)
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->options['extensionConfiguration'];

        $matches = preg_match_all(self::PATTERN_CAPITALCHAR, $passwordChangeRequest->getPassword());

        if ($matches >= $configuration->getMinimumCapitalCharacters()) {
            return;
        }

        $this->addTranslatedError(self::ERROR_CODE);
    }
}
