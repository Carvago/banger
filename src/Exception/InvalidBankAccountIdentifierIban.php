<?php

declare(strict_types=1);

namespace Eag\Banger\Exception;

use Eag\Banger\BankAccount\BankAccount;

final class InvalidBankAccountIdentifierIban extends InvalidBankAccountIdentifier
{
    public static function create(BankAccount $bankAccount, string $iban): self
    {
        return new self("IBAN '$iban' is not valid for bank account {$bankAccount->country} {$bankAccount->number}");
    }
}
