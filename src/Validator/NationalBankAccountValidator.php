<?php

declare(strict_types=1);

namespace Eag\Banger\Validator;

use Eag\Banger\Exception\InvalidBankAccountNumber;

interface NationalBankAccountValidator
{
    /**
     * @throws InvalidBankAccountNumber
     */
    public function validate(string $bankAccount): void;

    public function validatedCountry(): string;
}
