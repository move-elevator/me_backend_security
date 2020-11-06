<?php

namespace MoveElevator\MeBackendSecurity\Factory;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;

/**
 * @package MoveElevator\MeBackendSecurity\Factory
 */
class LoginProviderRedirectFactory
{
    protected const BASE_URL = 'index.php';

    /**
     * @param string $username
     * @param array  $errors
     * @param array  $messages
     *
     * @return LoginProviderRedirect
     */
    public static function create(
        string $username = '',
        array $errors = [],
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

        $loginProviderRedirect = new LoginProviderRedirect();
        $loginProviderRedirect->setUrl(self::BASE_URL . '?' . http_build_query($parameter));

        return $loginProviderRedirect;
    }
}
