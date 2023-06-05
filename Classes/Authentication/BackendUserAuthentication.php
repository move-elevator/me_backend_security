<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Authentication;

use MoveElevator\MeBackendSecurity\Service\MfaService;
use TYPO3\CMS\Core\Authentication\Mfa\MfaRequiredException;
use TYPO3\CMS\Core\Http\ServerRequestFactory;

class BackendUserAuthentication extends \TYPO3\CMS\Core\Authentication\BackendUserAuthentication
{
    private MfaService $mfaService;

    public function __construct()
    {
        parent::__construct();
        $this->mfaService = new MfaService();
    }

    protected function evaluateMfaRequirements(): void
    {
        try {
            parent::evaluateMfaRequirements();
        } catch (MfaRequiredException $mfaRequiredException) {
            $request = $GLOBALS['TYPO3_REQUEST'] ?? ServerRequestFactory::fromGlobals();
            $token = $request->getParsedBody()['tx_mebackendsecurity']['mfaToken'] ?? null;

            if ($token !== $this->mfaService->getMfaToken($this)) {
                throw $mfaRequiredException;
            }
        }
    }
}
