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
     * @param int $errorCode
     */
    protected function addTranslatedError($errorCode)
    {
        $this->addError(
            $this->translateErrorMessage(
                'error.' . $errorCode,
                'me_backend_security'
            ),
            $errorCode
        );
    }
}
