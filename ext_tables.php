<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$TCA['be_users']['columns']['password']['config']['eval'] =
    'required,' . \MoveElevator\MeBackendSecurity\Evaluation\PasswordEvaluator::class . ',saltedPassword,password';
