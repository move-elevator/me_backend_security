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
        $mfaToken = 'mfatoken';
        $messages = ['message1', 'message2', 'message3'];

        $loginProviderRedirect = LoginProviderRedirectFactory::create($username);
        $this->assertEquals($loginProviderRedirect->getUrl(), 'index.php?r=1&u=' . $username);

        $loginProviderRedirect = LoginProviderRedirectFactory::create($username, $errorCodes);
        $this->assertEquals(
            $loginProviderRedirect->getUrl(),
            'index.php?r=1&u=' . $username . '&e=' . urlencode(base64_encode(serialize($errorCodes)))
        );

        $loginProviderRedirect = LoginProviderRedirectFactory::create($username, $errorCodes, $mfaToken);
        $this->assertEquals(
            $loginProviderRedirect->getUrl(),
            'index.php?r=1&u=' . $username . '&e=' . urlencode(base64_encode(serialize($errorCodes))) .
            '&x=' . urlencode(base64_encode(serialize($mfaToken)))
        );

        $loginProviderRedirect = LoginProviderRedirectFactory::create($username, $errorCodes, '', $messages);
        $this->assertEquals(
            $loginProviderRedirect->getUrl(),
            'index.php?r=1&u=' . $username . '&e=' . urlencode(base64_encode(serialize($errorCodes))) .
            '&m=' . urlencode(base64_encode(serialize($messages)))
        );
    }
}
