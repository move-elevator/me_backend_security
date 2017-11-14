<?php

namespace MoveElevator\MeBackendSecurity\Factory;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use TYPO3\CMS\Rsaauth\RsaEncryptionDecoder;

/**
 * @package MoveElevator\MeBackendSecurity\Factory
 */
class PasswordChangeRequestFactory
{
    /**
     * @param array                $passwordChangeRequestParameters
     * @param RsaEncryptionDecoder $rsaEncryptionDecoder
     *
     * @return PasswordChangeRequest
     */
    public static function create($passwordChangeRequestParameters, RsaEncryptionDecoder $rsaEncryptionDecoder)
    {
        if (empty($passwordChangeRequestParameters['password']) ||
            empty($passwordChangeRequestParameters['password_confirmation'])
        ) {
            throw new \InvalidArgumentException(
                'The given arguments are incomplete!'
            );
        }

        $passwordChange = new PasswordChangeRequest();
        $passwordChange->setPassword(
            $rsaEncryptionDecoder->decrypt($passwordChangeRequestParameters['password'])
        );
        $passwordChange->setPasswordConfirmation(
            $rsaEncryptionDecoder->decrypt($passwordChangeRequestParameters['password_confirmation'])
        );

        return $passwordChange;
    }
}
