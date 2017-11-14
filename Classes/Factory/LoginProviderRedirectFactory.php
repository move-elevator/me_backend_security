<?php

namespace MoveElevator\MeBackendSecurity\Factory;

use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;

/**
 * @package MoveElevator\MeBackendSecurity\Factory
 */
class LoginProviderRedirectFactory
{
    const BASE_URL = 'index.php';

    /**
     * @param string|null $username
     * @param int|null    $errorCode
     *
     * @return LoginProviderRedirect
     */
    public static function create($username = null, $errorCode = null)
    {
        $parameter = [
            'r' => 1
        ];

        if (empty($username) === false) {
            $parameter['u'] = $username;
        }

        if (empty($errorCode) === false) {
            $parameter['e'] = $errorCode;
        }

        $loginProviderRedirect = new LoginProviderRedirect();
        $loginProviderRedirect->setUrl(self::BASE_URL . '?' . http_build_query($parameter));

        return $loginProviderRedirect;
    }
}
