<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

trait PasswordChangeRequestFixture
{
    protected string $currentPassword = 'foo';
    protected string $password = 'fooBar';
    protected string $passwordConfirmation = 'fooBar';

    protected function getPasswordChangeRequestFixture(): PasswordChangeRequest
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setCurrentPassword($this->currentPassword);
        $passwordChangeRequest->setPassword($this->password);
        $passwordChangeRequest->setPasswordConfirmation($this->passwordConfirmation);

        return $passwordChangeRequest;
    }

    protected function getRawPasswordChangeRequestFixture(): array
    {
        return [
            'changeRequestParameters' => [
                'password' => $this->password,
                'password2' => $this->passwordConfirmation
            ],
            'currentPassword' => $this->currentPassword
        ];
    }
}
