<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Service;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MfaService
{
    public function getMfaToken(BackendUserAuthentication $backendUserAuthentication): string
    {
        return GeneralUtility::hmac(
            $backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] . '|'
            . $backendUserAuthentication->user['mfa'] . '|'
            . $backendUserAuthentication->user['email'] . '|'
            . $backendUserAuthentication->user['uid'],
            'mfa-token'
        );
    }
}
