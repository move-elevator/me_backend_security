<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Domain\Model;

class PasswordChangeRequest
{
    protected string $currentPassword = '';
    protected string $password = '';
    protected string $passwordConfirmation = '';

    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    public function setCurrentPassword(string $currentPassword): void
    {
        $this->currentPassword = $currentPassword;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getPasswordConfirmation(): string
    {
        return $this->passwordConfirmation;
    }

    public function setPasswordConfirmation(string $passwordConfirmation): void
    {
        $this->passwordConfirmation = $passwordConfirmation;
    }
}
