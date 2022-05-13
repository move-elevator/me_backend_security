<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Validation\Validator;

use InvalidArgumentException;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;

class CompositeValidator extends AbstractValidator
{
    protected array $validators = [];

    public function append(AbstractValidator $validator): void
    {
        $this->validators[] = $validator;
    }

    protected function isValid(mixed $value): void
    {
        if (false === $value instanceof PasswordChangeRequest) {
            throw new InvalidArgumentException(
                'The given value is not from type ' . PasswordChangeRequest::class . '.',
                1512480115
            );
        }

        /** @var AbstractValidator $validator */
        foreach ($this->validators as $validator) {
            $this->executeValidator($validator, $value);
        }
    }

    private function executeValidator(AbstractValidator $validator, mixed $value): void
    {
        $result = $validator->validate($value);

        if (false === $result->hasErrors()) {
            return;
        }

        foreach ($result->getErrors() as $error) {
            $this->addError(
                $error->getMessage(),
                $error->getCode(),
                $error->getArguments()
            );
        }
    }
}
