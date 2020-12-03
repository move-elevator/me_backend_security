<?php
declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
final class CapitalCharactersValidator extends AbstractValidator
{
    protected const PATTERN_CAPITALCHAR = '/[A-ZÄÖÜ]/';
    protected const ERROR_CODE = 1510742745;

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     */
    protected function isValid($passwordChangeRequest): void
    {
        /** @var ExtensionConfiguration $configuration */
        $configuration = $this->options['extensionConfiguration'];

        $matches = preg_match_all(self::PATTERN_CAPITALCHAR, $passwordChangeRequest->getPassword());

        if ($matches >= $configuration->getMinimumCapitalCharacters()) {
            return;
        }

        $this->addTranslatedError(
            self::ERROR_CODE,
            $configuration->getMinimumCapitalCharacters()
        );
    }
}
