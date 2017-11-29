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
        $compositeValidator = self::createEmptyCompositeValidator($objectManager, $extensionConfiguration);

        if (empty($rawTypoScriptSetup['config.']['tx_mebackendsecurity.']['validators.'])) {
            throw new \InvalidArgumentException(
                'No typoscript setup for validator initialization.'
            );
        }

        $validators = $rawTypoScriptSetup['config.']['tx_mebackendsecurity.']['validators.'];

        foreach ($validators as $validatorClass) {
            /** @var AbstractValidator $validator */
            $validator = $objectManager->get(
                $validatorClass,
                ['extensionConfiguration' => $extensionConfiguration]
            );
            $compositeValidator->append($validator);
        }
    }

    /**
     * @param ObjectManager          $objectManager
     * @param ExtensionConfiguration $extensionConfiguration
     *
     * @return CompositeValidator
     */
    private static function createEmptyCompositeValidator(
        ObjectManager $objectManager,
        ExtensionConfiguration $extensionConfiguration
    ) {
        /** @var CompositeValidator $compositeValidator */
        $compositeValidator = $objectManager->get(
            CompositeValidator::class,
            ['extensionConfiguration' => $extensionConfiguration]
        );

        return $compositeValidator;
    }
}
