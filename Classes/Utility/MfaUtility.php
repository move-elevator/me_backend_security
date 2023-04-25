<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Utility;

use DateInterval;
use DateTime;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MfaUtility
{
    public static function getMfaToken(BackendUserAuthentication $user): string
    {
        $hash = GeneralUtility::hmac((string)$user->user['tx_mebackendsecurity_lastpasswordchange'] . '|'
            . $user->user['mfa'] . '|'
            . $user->user['email'] . '|'
            . (string)$user->user['uid'], 'mfa-token');
        return $hash;
    }
}
