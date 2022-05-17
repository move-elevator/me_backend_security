<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures;

use MoveElevator\MeBackendSecurity\Validation\Validator\CapitalCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\DigitsValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\LowercaseCharactersValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\PasswordConfirmationValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\PasswordLengthValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\SpecialCharactersValidator;

trait TypoScriptSetupFixture
{
    protected array $validators = [
        CapitalCharactersValidator::class,
        DigitsValidator::class,
        LowercaseCharactersValidator::class,
        PasswordConfirmationValidator::class,
        PasswordLengthValidator::class,
        SpecialCharactersValidator::class
    ];

    protected function getRawTypoScriptSetupFixture(): array
    {
        return [
            'config.' => [
                'tx_mebackendsecurity.' => [
                    'validators.' => $this->validators,
                ],
            ],
        ];
    }
}
