<?php

namespace MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
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
    protected function getAddressFixtureObject()
    {
        $passwordChangeRequest = new PasswordChangeRequest();
        $passwordChangeRequest->setPassword($this->password);
        $passwordChangeRequest->setPasswordConfirmation($this->passwordConfirmation);

        return $passwordChangeRequest;
    }
}
