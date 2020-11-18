<?php
declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
abstract class AbstractValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
    /**
     * @var array
     */
    protected $supportedOptions = [
        'extensionConfiguration' => [0, 'The extension configuration object', ExtensionConfiguration::class, true]
    ];

    /**
     * @param int  $errorCode
     * @param int  $minimum
     */
    protected function addTranslatedError(int $errorCode, int $minimum = 0): void
    {
        $languageKey = 'error.' . $errorCode;
        $arguments = [
            'minimum' => $minimum,
            'singular' => false
        ];

        if ($minimum === 1) {
            $languageKey = 'error.' . $errorCode . '.singular';
            $arguments['singular'] = true;
        }

        $this->addError(
            $this->translateErrorMessage(
                $languageKey,
                'me_backend_security',
                $arguments
            ),
            $errorCode,
            $arguments
        );
    }
}
