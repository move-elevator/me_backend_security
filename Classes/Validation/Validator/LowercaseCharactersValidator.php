<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

class LowercaseCharactersValidator extends AbstractValidator
{
    private const PATTERN_LOWERCASECHAR = '/[a-zäöü]/u';
    private const ERROR_CODE = 1510742746;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     */
    protected function isValid($passwordChangeRequest): void
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->options['extensionConfiguration'];

        $matches = preg_match_all(self::PATTERN_LOWERCASECHAR, $passwordChangeRequest->getPassword());

        if ($matches >= $configuration->getMinimumLowercaseCharacters()) {
            return;
        }

        $this->addTranslatedError(
            self::ERROR_CODE,
            $configuration->getMinimumLowercaseCharacters()
        );
    }
}
