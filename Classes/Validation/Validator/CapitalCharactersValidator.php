<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

class CapitalCharactersValidator extends AbstractValidator
{
    private const PATTERN_CAPITAL_CHAR = '/[A-ZÄÖÜ]/';
    private const ERROR_CODE = 1510742745;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     */
    protected function isValid($passwordChangeRequest): void
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->options['extensionConfiguration'];

        $matches = preg_match_all(self::PATTERN_CAPITAL_CHAR, $passwordChangeRequest->getPassword());

        if ($matches >= $configuration->getMinimumCapitalCharacters()) {
            return;
        }

        $this->addTranslatedError(
            self::ERROR_CODE,
            $configuration->getMinimumCapitalCharacters()
        );
    }
}
