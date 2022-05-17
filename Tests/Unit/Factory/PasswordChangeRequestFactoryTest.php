<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use InvalidArgumentException;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\PasswordChangeRequestFixture;
use PHPUnit\Framework\TestCase;

class PasswordChangeRequestFactoryTest extends TestCase
{
    use PasswordChangeRequestFixture;

    public function testCreateObjectFromValidArguments(): void
    {
        $rawPasswordChangeRequest = $this->getRawPasswordChangeRequestFixture();
        $expectedPasswordChangeRequest = $this->getPasswordChangeRequestFixture();

        $passwordChangeRequest = PasswordChangeRequestFactory::create(
            $rawPasswordChangeRequest['changeRequestParameters'],
            $rawPasswordChangeRequest['currentPassword']
        );

        $this->assertEquals($passwordChangeRequest->getPassword(), $expectedPasswordChangeRequest->getPassword());
        $this->assertEquals(
            $passwordChangeRequest->getPasswordConfirmation(),
            $expectedPasswordChangeRequest->getPasswordConfirmation()
        );
    }

    public function testCreateObjectFromInvalidArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PasswordChangeRequestFactory::create([]);
    }
}
