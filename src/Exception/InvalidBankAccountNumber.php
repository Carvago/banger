<?php

declare(strict_types=1);

namespace Eag\Banger\Exception;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class InvalidBankAccountNumber extends RuntimeException
{
    private function __construct(public string $bankAccount, public string $country)
    {
        parent::__construct("Bank account number '$bankAccount' is not valid in country {$country}");
    }

    public static function create(string $bankAccount, string $country): self
    {
        return new self($bankAccount, $country);
    }
}
