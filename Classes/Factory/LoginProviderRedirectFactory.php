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
     * @param array       $errorCodes
     *
     * @return LoginProviderRedirect
     */
    public static function create($username = null, $errorCodes = [])
    {
        $parameter = [
            'r' => 1
        ];

        if (empty($username) === false) {
            $parameter['u'] = $username;
        }

        if (empty($errorCodes) === false) {
            $parameter['e'] = urlencode(base64_encode(serialize($errorCodes)));
        }

        $loginProviderRedirect = new LoginProviderRedirect();
        $loginProviderRedirect->setUrl(self::BASE_URL . '?' . http_build_query($parameter));

        return $loginProviderRedirect;
    }
}
