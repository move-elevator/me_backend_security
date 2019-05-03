<?php

namespace MoveElevator\MeBackendSecurity\Factory;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

/**
 * @package MoveElevator\MeBackendSecurity\Factory
 */
class PasswordChangeRequestFactory
{
    /**
     * @param array  $changeRequestParameters
     * @param string $currentPassword
     *
     * @return PasswordChangeRequest
     */
    public static function create(
        array $changeRequestParameters,
        ?string $currentPassword = null
    ): PasswordChangeRequest {
        if (empty($changeRequestParameters['password']) ||
            empty($changeRequestParameters['password2'])
        ) {
            throw new \InvalidArgumentException(
                'Some request parameters are missing for password change.',
                1512481285
            );
        }

        $passwordChange = new PasswordChangeRequest();

        if (empty($currentPassword) === false) {
            $passwordChange->setCurrentPassword($currentPassword);
        }

        $passwordChange->setPassword($changeRequestParameters['password']);
        $passwordChange->setPasswordConfirmation($changeRequestParameters['password2']);

        return $passwordChange;
    }
}
