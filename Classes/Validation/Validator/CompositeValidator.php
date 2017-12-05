<?php

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use TYPO3\CMS\Extbase\Error\Error;

/**
 * @package MoveElevator\MeBackendSecurity\Validation\Validator
 */
class CompositeValidator extends AbstractValidator
{
    /**
     * @var array
     */
    protected $validators = [];

    /**
     * @param AbstractValidator $validator
     */
    public function append(AbstractValidator $validator)
    {
        $this->validators[] = $validator;
    }

    /**
     * @param mixed $value
     */
    protected function isValid($value)
    {
        if ($value instanceof PasswordChangeRequest === false) {
            throw new \InvalidArgumentException(
                'The given argument is invalid.'
            );
        }

        /** @var AbstractValidator $validator */
        foreach ($this->validators as $validator) {
            $this->executeValidator($validator, $value);
        }
    }

    /**
     * @param AbstractValidator $validator
     * @param mixed             $value
     */
    private function executeValidator($validator, $value)
    {
        $result = $validator->validate($value);

        if ($result->hasErrors() === false) {
            return;
        }

        /** @var Error $error */
        foreach ($result->getErrors() as $error) {
            $this->addError(
                $error->getMessage(),
                $error->getCode()
            );
        }
    }
}
