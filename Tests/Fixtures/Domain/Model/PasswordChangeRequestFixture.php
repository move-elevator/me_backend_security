<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Fixture\Domain\Model
 */
trait PasswordChangeRequestFixture
{
    /**
     * @var string
     */
    protected $password = 'password';

    /**
     * @var string
     */
    protected $passwordConfirmation = 'password';

    /**
     * @return PasswordChangeRequest
     */
    protected function getPasswordChangeRequestFixture()
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword($this->password);
        $passwordChangeRequest->setPasswordConfirmation($this->passwordConfirmation);

        return $passwordChangeRequest;
    }

    /**
     * @return array
     */
    protected function getRawPasswordChangeRequestFixture()
    {
        return [
            'password' => $this->password,
            'password2' => $this->passwordConfirmation
        ];
    }
}
