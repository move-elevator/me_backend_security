<?php

namespace MoveElevator\MeBackendSecurity\Tests\Unit\Domain\Model;

use MoveElevator\MeBackendSecurity\Factory\LoginProviderRedirectFactory;
use PHPUnit\Framework\TestCase;

class LoginProviderRedirectFactoryTest extends TestCase
{
    public function testCreateObjectWithoutArguments(): void
    {
        $loginProviderRedirect = LoginProviderRedirectFactory::create();

        $this->assertEquals($loginProviderRedirect->getUrl(), 'index.php?r=1');
    }

    public function testCreateObjectFromValidArguments(): void
    {
        $username = 'testuser';
        $errorCodes = [100, 200, 300];

        $loginProviderRedirect = LoginProviderRedirectFactory::create($username);
        $this->assertEquals($loginProviderRedirect->getUrl(), 'index.php?r=1&u=' . $username);

        $loginProviderRedirect = LoginProviderRedirectFactory::create($username, $errorCodes);
        $this->assertEquals(
            $loginProviderRedirect->getUrl(),
            'index.php?r=1&u=' . $username . '&e=' . urlencode(base64_encode(serialize($errorCodes)))
        );
    }
}
