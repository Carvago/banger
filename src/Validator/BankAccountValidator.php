<?php

declare(strict_types=1);

namespace Eag\Banger\Validator;

use Eag\Banger\Exception\InvalidBankAccountNumber;

interface BankAccountValidator
{
    /**
     * @throws InvalidBankAccountNumber
     */
    public function validate(string $bankAccount, string $country): void;
}
