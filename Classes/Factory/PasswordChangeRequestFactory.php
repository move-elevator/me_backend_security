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
     * @param array                $changeRequestParameters
     * @param RsaEncryptionDecoder $rsaEncryptionDecoder
     *
     * @return PasswordChangeRequest
     */
    public static function create(array $changeRequestParameters, RsaEncryptionDecoder $rsaEncryptionDecoder)
    {
        if (empty($changeRequestParameters['password']) ||
            empty($changeRequestParameters['password2'])
        ) {
            throw new \InvalidArgumentException(
                'Some request parameters are missing for password change.',
                1512481285
            );
        }

        $passwordChange = new PasswordChangeRequest();
        $passwordChange->setPassword(
            $rsaEncryptionDecoder->decrypt($changeRequestParameters['password'])
        );
        $passwordChange->setPasswordConfirmation(
            $rsaEncryptionDecoder->decrypt($changeRequestParameters['password2'])
        );

        return $passwordChange;
    }
}
