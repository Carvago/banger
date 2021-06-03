<?php

declare(strict_types=1);

namespace Eag\Banger\Determiner;

use Eag\Banger\Exception\RuntimeException;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class UnknownBankAccountCountry extends RuntimeException
{
    private function __construct(public string $bankAccount)
    {
        parent::__construct("Cannot determine country for bank account $bankAccount");
    }

    public static function create(string $bankAccount): self
    {
        return new self($bankAccount);
    }
}
