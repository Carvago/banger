<?php

declare(strict_types=1);

namespace Eag\Banger\Validator;

use Eag\Banger\BankAccount\BankAccount;
use Eag\Banger\Exception\InvalidBankAccountIdentifier;

interface BankAccountIdentifierValidator
{
    /**
     * @throws InvalidBankAccountIdentifier
     */
    public function validate(BankAccount $bankAccount, ?string $iban, ?string $swift): void;
}
