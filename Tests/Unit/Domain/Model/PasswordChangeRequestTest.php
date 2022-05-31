<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\PasswordChangeRequestFixture;
use PHPUnit\Framework\TestCase;

class PasswordChangeRequestTest extends TestCase
{
    use PasswordChangeRequestFixture;

    public function testNoManipulationInSetterAndGetter(): void
    {
        $passwordChangeRequest = $this->getPasswordChangeRequestFixture();

        $this->assertEquals($this->currentPassword, $passwordChangeRequest->getCurrentPassword());
        $this->assertEquals($this->password, $passwordChangeRequest->getPassword());
        $this->assertEquals($this->passwordConfirmation, $passwordChangeRequest->getPasswordConfirmation());
    }
}
