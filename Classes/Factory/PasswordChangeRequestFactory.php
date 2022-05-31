<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Factory;

use InvalidArgumentException;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

class PasswordChangeRequestFactory
{
    public static function create(
        array $changeRequestParameters,
        ?string $currentPassword = null
    ): PasswordChangeRequest {
        if (true === empty($changeRequestParameters['password']) ||
            true === empty($changeRequestParameters['password2'])
        ) {
            throw new InvalidArgumentException(
                'Some request parameters are missing for password change.',
                1512481285
            );
        }

        $passwordChange = new PasswordChangeRequest();

        if (false === empty($currentPassword)) {
            $passwordChange->setCurrentPassword($currentPassword);
        }

        $passwordChange->setPassword($changeRequestParameters['password']);
        $passwordChange->setPasswordConfirmation($changeRequestParameters['password2']);

        return $passwordChange;
    }
}
