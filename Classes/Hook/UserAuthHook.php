<?php

namespace MoveElevator\MeBackendSecurity\Hook;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Rsaauth\RsaEncryptionDecoder;

/**
 * @package MoveElevator\MeBackendSecurity\Hook
 */
class UserAuthHook
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var RsaEncryptionDecoder
     */
    protected $rsaEncryptionDecoder;

    /**
     * @param array $params
     * @param BackendUserAuthentication $pObj
     */
    public function postUserLookUp($params, $pObj)
    {
        if ($this->isLocalBackendUserAuthentificaton($pObj) === false) {
            return;
        }

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->rsaEncryptionDecoder = $this->objectManager->get(RsaEncryptionDecoder::class);

        $this->changePasswordIfRequested($pObj);
        $this->checkPasswordLifeTime($pObj);
    }

    /**
     * @param $pObj
     *
     * @return bool
     */
    private function isLocalBackendUserAuthentificaton($pObj)
    {
        if (!$pObj instanceof BackendUserAuthentication) {
            return false;
        }

        if (empty($pObj->user) === true) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    private function changePasswordIfRequested(BackendUserAuthentication $pObj)
    {
        $resetFormVars = GeneralUtility::_GP('tx_mebackendsecurity');

        if (empty($resetFormVars['password_new']) === true ||
            empty($resetFormVars['password_confirmation']) === true
        ) {
            return;
        }

        $resetFormVars['password_new'] = $this->rsaEncryptionDecoder->decrypt(
            $resetFormVars['password_new']
        );

        $resetFormVars['password_confirmation'] = $this->rsaEncryptionDecoder->decrypt(
            $resetFormVars['password_confirmation']
        );

        $userExists = $GLOBALS['TYPO3_DB']->exec_SELECTcountRows(
            'uid',
            'be_users',
            'username=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($pObj->user['username'], 'be_users')
        );

        if ($userExists) {
            $hashedPassword = $this->getHashedPassword($resetFormVars['password_new']);
            $userFields = [
                'password' => $hashedPassword,
                'tx_mebackendsecurity_lastpasswordchange' => $GLOBALS['EXEC_TIME']
            ];

            $GLOBALS['TYPO3_DB']->exec_UPDATEquery('be_users', 'uid=' . $pObj->user['uid'], $userFields);

            $pObj->user['tx_mebackendsecurity_lastpasswordchange'] = $GLOBALS['EXEC_TIME'];

            return;
        }

        $this->redirectToForm($pObj, 100);
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

        $this->redirectToForm($pObj);
    }

    /**
     * @param BackendUserAuthentication $pObj
     * @param int|null                  $errorCode
     */
    private function redirectToForm(BackendUserAuthentication $pObj, int $errorCode = null)
    {
        $url = 'index.php?r=1';

        if (empty($pObj->user['username']) === false) {
            $url .= '&u=' . $pObj->user['username'];
        }

        if (empty($errorCode) === false) {
            $url .= '&e=' . $errorCode;
        }

        $user = $pObj->user;

        if (!$GLOBALS['LANG']) {
            $userUc = unserialize($user['uc']);
            $GLOBALS['LANG'] = $this->objectManager->get(LanguageService::class);
            $GLOBALS['LANG']->init($userUc['lang']);
        }

        $pObj->logoff();

        HttpUtility::redirect($url);
    }



    /**
     * @param string $password
     * @return string
     */
    protected function getHashedPassword($password)
    {
        $saltFactory = \TYPO3\CMS\Saltedpasswords\Salt\SaltFactory::getSaltingInstance(null, 'BE');

        return $saltFactory->getHashedPassword($password);
    }
}
