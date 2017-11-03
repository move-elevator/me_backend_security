<?php

namespace MoveElevator\MeBackendSecurity\Hook;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Lang\LanguageService;

/**
 * @package MoveElevator\MeBackendSecurity\Hook
 */
class UserAuthHook
{
    /**
     * @param array $params
     * @param BackendUserAuthentication $pObj
     */
    public function postUserLookUp($params, $pObj)
    {
        if ($pObj instanceof BackendUserAuthentication && empty($pObj->user) === false) {
            $this->checkPasswordLifeTime($pObj);

            return;
        }

        return;
    }

    /**
     * @param BackendUserAuthentication $pObj
     */
    private function checkPasswordLifeTime(BackendUserAuthentication $pObj)
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var \TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility $configurationUtility */
        $configurationUtility = $objectManager->get('TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility');
        $configuration = $configurationUtility->getCurrentConfiguration('me_backend_security');
        $validUntil = (int)$configuration['validUntil']['value'];

        $now = new \DateTime();
        $expireDeathLine = new \DateTime();
        $expireDeathLine->setTimestamp(
            intval($pObj->user['tx_mebackendsecurity_lastpasswordchange'])
        );
        $expireDeathLine->add(
            new \DateInterval('P' . $validUntil . 'D')
        );

        if ($now <= $expireDeathLine) {
            return;
        }

        $user = $pObj->user;

        if (!$GLOBALS['LANG']) {
            $userUc = unserialize($user['uc']);
            $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageService::class);
            $GLOBALS['LANG']->init($userUc['lang']);
        }

        $pObj->logoff();

        HttpUtility::redirect('index.php?r=1&u=' . $user['username']);
    }
}
