<?php

declare(strict_types=1);

namespace Eag\Banger\Validator\NationalBankAccountIdentifierValidator;

use Eag\Banger\BankAccount\BankAccount;
use Eag\Banger\Exception\InvalidBankAccountIdentifier;

interface NationalBankAccountIdentifierValidator
{
    /**
     * @throws InvalidBankAccountIdentifier
     */
    public function validate(BankAccount $bankAccount, ?string $iban, ?string $swift): void;

    public function validatedCountry(): string;
}
