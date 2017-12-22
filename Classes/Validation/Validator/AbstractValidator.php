<?php

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
     * @param bool $singular
     */
    protected function addTranslatedError($errorCode, $minimum = 0)
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
                (string)$languageKey,
                'me_backend_security',
                $arguments
            ),
            (int)$errorCode,
            $arguments
        );
    }
}
