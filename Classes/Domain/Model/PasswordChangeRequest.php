<?php

namespace MoveElevator\MeBackendSecurity\Domain\Model;

/**
 * @package MoveElevator\MeBackendSecurity\Domain\Model
 */
class PasswordChangeRequest
{
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
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPasswordConfirmation()
    {
        return $this->passwordConfirmation;
    }

    /**
     * @param string $passwordConfirmation
     */
    public function setPasswordConfirmation($passwordConfirmation)
    {
        $this->passwordConfirmation = $passwordConfirmation;
    }
}
