<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Factory;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Validation\Validator\AbstractValidator;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class CompositeValidatorFactory
{
    public static function create(
        ObjectManager $objectManager,
        ExtensionConfiguration $extensionConfiguration,
        array $rawTypoScriptSetup
    ): CompositeValidator {
        $compositeValidator = self::createEmptyCompositeValidator($objectManager, $extensionConfiguration);

        if (empty($rawTypoScriptSetup['config.']['tx_mebackendsecurity.']['validators.'])) {
            throw new \InvalidArgumentException(
                'No typoscript setup for validator initialization.',
                1512481167
            );
        }

        $validators = $rawTypoScriptSetup['config.']['tx_mebackendsecurity.']['validators.'];

        foreach ($validators as $validatorClass) {
            /** @var AbstractValidator $validator */
            $validator = $objectManager->get(
                (string)$validatorClass,
                ['extensionConfiguration' => $extensionConfiguration]
            );
            $compositeValidator->append($validator);
        }

        return $compositeValidator;
    }

    private static function createEmptyCompositeValidator(
        ObjectManager $objectManager,
        ExtensionConfiguration $extensionConfiguration
    ): CompositeValidator {
        /** @var CompositeValidator $compositeValidator */
        $compositeValidator = $objectManager->get(
            CompositeValidator::class,
            ['extensionConfiguration' => $extensionConfiguration]
        );

        return $compositeValidator;
    }
}
