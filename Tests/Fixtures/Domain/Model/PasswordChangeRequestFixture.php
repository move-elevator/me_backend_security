<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model
 */
trait PasswordChangeRequestFixture
{
    /**
     * @var string
     */
    protected $currentPassword = 'foo';

    /**
     * @var string
     */
    protected $password = 'fooBar';

    /**
     * @var string
     */
    protected $passwordConfirmation = 'fooBar';

    /**
     * @return PasswordChangeRequest
     */
    protected function getPasswordChangeRequestFixture()
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setCurrentPassword($this->currentPassword);
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
            'changeRequestParameters' => [
                'password' => $this->password,
                'password2' => $this->passwordConfirmation
            ],
            'currentPassword' => $this->currentPassword
        ];
    }
}
