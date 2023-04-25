<?php

namespace MoveElevator\MeBackendSecurity\Authentication;

use MoveElevator\MeBackendSecurity\Utility\MfaUtility;
use TYPO3\CMS\Core\Authentication\Mfa\MfaRequiredException;
use TYPO3\CMS\Core\Http\ServerRequestFactory;

class BackendUserAuthentication extends \TYPO3\CMS\Core\Authentication\BackendUserAuthentication {

    protected function evaluateMfaRequirements(): void
    {
        try {
            parent::evaluateMfaRequirements();
        } catch (MfaRequiredException $mfaRequiredException) {
            $request = $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
            $token = $request->getParsedBody()['tx_mebackendsecurity']['mfaToken'] ?? null;
            if ($token !== MfaUtility::getMfaToken($this)) {
                throw $mfaRequiredException;
            }
        }
    }
}
