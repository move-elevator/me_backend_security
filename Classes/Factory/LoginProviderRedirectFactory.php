<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Factory;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;

class LoginProviderRedirectFactory
{
    protected const BASE_URL = 'index.php';

    public static function create(
        string $username = '',
        array $errors = [],
        string $mfaToken = '',
        array $messages = []
    ): LoginProviderRedirect {
        $parameter = [
            'r' => 1
        ];

        if (empty($username) === false) {
            $parameter['u'] = $username;
        }

        if (empty($errors) === false) {
            $parameter['e'] = urlencode(base64_encode(serialize($errors)));
        }

        if (empty($messages) === false) {
            $parameter['m'] = urlencode(base64_encode(serialize($messages)));
        }

        if (empty($mfaToken) === false) {
            $parameter['x'] = urlencode(base64_encode(serialize($mfaToken)));
        }

        $loginProviderRedirect = new LoginProviderRedirect();
        $loginProviderRedirect->setUrl(self::BASE_URL . '?' . http_build_query($parameter));

        return $loginProviderRedirect;
    }
}
