<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator as CoreAbstractValidator;

abstract class AbstractValidator extends CoreAbstractValidator
{
    /**
     * @var array
     */
    protected $supportedOptions = [
        'extensionConfiguration' => [
            0,
            'The extension configuration object',
            ExtensionConfiguration::class,
            true
        ],
    ];

    protected function addTranslatedError(int $errorCode, int $minimum = 0): void
    {
        $languageKey = 'error.' . $errorCode;
        $arguments = [
            'minimum' => $minimum,
            'singular' => false
        ];

        if (1 === $minimum) {
            $languageKey = 'error.' . $errorCode . '.singular';
            $arguments['singular'] = true;
        }

        $this->addError(
            $this->translateErrorMessage(
                $languageKey,
                ExtensionConfiguration::EXT_KEY,
                $arguments
            ),
            $errorCode,
            $arguments
        );
    }
}
