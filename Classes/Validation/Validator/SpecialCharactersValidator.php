<?php

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
class SpecialCharactersValidator extends AbstractValidator
{
    protected const PATTERN_SPECIALCHAR = '/[\\\[\]\/\-(){}#?!$%&=*+~,.;:<>_]/';
    protected const ERROR_CODE = 1510742743;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     */
    protected function isValid($passwordChangeRequest): void
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->options['extensionConfiguration'];

        $matches = preg_match_all(self::PATTERN_SPECIALCHAR, $passwordChangeRequest->getPassword());

        if ($matches >= $configuration->getMinimumSpecialCharacters()) {
            return;
        }

        $this->addTranslatedError(
            self::ERROR_CODE,
            $configuration->getMinimumSpecialCharacters()
        );
    }
}
