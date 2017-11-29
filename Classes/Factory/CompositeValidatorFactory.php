<?php

namespace MoveElevator\MeBackendSecurity\Factory;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Validation\Validator\AbstractValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @package MoveElevator\MeBackendSecurity\Factory
 */
class CompositeValidatorFactory
{
    /**
     * @param ObjectManager          $objectManager
     * @param ExtensionConfiguration $extensionConfiguration
     * @param array                  $rawTypoScriptSetup
     *
     * @return CompositeValidator
     */
    public static function create(
        ObjectManager $objectManager,
        ExtensionConfiguration $extensionConfiguration,
        $rawTypoScriptSetup
    ) {
        if (empty($rawTypoScriptSetup['config.']['tx_mebackendsecurity.']['validators.'])) {
            throw new \InvalidArgumentException(
                'No typoscript setup for validator initialization.'
            );
        }

        $validators = $rawTypoScriptSetup['config.']['tx_mebackendsecurity.']['validators.'];
        $validatorOptions = [
            'extensionConfiguration' => $extensionConfiguration
        ];

        /** @var CompositeValidator $compositeValidator */
        $compositeValidator = $objectManager->get(CompositeValidator::class, $validatorOptions);

        foreach ($validators as $validatorClass) {
            /** @var AbstractValidator $validator */
            $validator = $objectManager->get($validatorClass, $validatorOptions);
            $compositeValidator->append($validator);
        }

        return $compositeValidator;
    }
}
