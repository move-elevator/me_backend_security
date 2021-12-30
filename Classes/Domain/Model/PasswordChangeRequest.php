<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Domain\Model;

class PasswordChangeRequest
{
    /**
     * @var string
     */
    protected $currentPassword = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var string
     */
    protected $passwordConfirmation = '';

    /**
     * @return string
     */
    public function getCurrentPassword(): string
    {
        return $this->currentPassword;
    }

    /**
     * @param string $currentPassword
     */
    public function setCurrentPassword(string $currentPassword): void
    {
        $this->currentPassword = $currentPassword;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPasswordConfirmation(): string
    {
        return $this->passwordConfirmation;
    }

    /**
     * @param string $passwordConfirmation
     */
    public function setPasswordConfirmation(string $passwordConfirmation): void
    {
        $this->passwordConfirmation = $passwordConfirmation;
    }
}
