<?php

declare(strict_types=1);

namespace Eag\Banger\BankAccount;

use Eag\Banger\Determiner\BankAccountCountryDeterminer;
use Eag\Banger\Determiner\UnknownBankAccountCountry;
use Eag\Banger\Exception\InvalidBankAccountNumber;
use Eag\Banger\Validator\BankAccountValidator;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
final class BankAccount
{
    private function __construct(public string $number, public string $country)
    {
    }

    /**
     * @throws InvalidBankAccountNumber
     * @throws UnknownBankAccountCountry
     */
    public static function create(
        BankAccountValidator $validator,
        BankAccountCountryDeterminer $determiner,
        string $number
    ): self {
        $country = $determiner->determine($number);
        $validator->validate($number, $country);

        return new self($number, $country);
    }
}
