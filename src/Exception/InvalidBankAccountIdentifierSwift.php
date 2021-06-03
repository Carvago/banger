<?php

declare(strict_types=1);

namespace Eag\Banger\Exception;

use Eag\Banger\BankAccount\BankAccount;

final class InvalidBankAccountIdentifierSwift extends InvalidBankAccountIdentifier
{
    public static function create(BankAccount $bankAccount, string $swift): self
    {
        return new self("IBAN '$swift' is not valid for bank account {$bankAccount->country} {$bankAccount->number}");
    }
}
