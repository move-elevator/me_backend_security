<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Tests\Fixtures\Domain\Model\LoginProviderRedirectFixture;
use PHPUnit\Framework\TestCase;

/**
 * @package MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model
 */
class LoginProviderRedirectTest extends TestCase
{
    use LoginProviderRedirectFixture;

    public function testNoManipulationInSetterAndGetter(): void
    {
        $loginProviderRedirect = $this->getLoginProviderRedirectFixture();

        $this->assertEquals($this->url, $loginProviderRedirect->getUrl());
    }
}
