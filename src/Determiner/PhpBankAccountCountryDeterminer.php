<?php

declare(strict_types=1);

namespace Eag\Banger\Determiner;

use Eag\Banger\Exception\InvalidBankAccountNumber;
use Eag\Banger\Validator\NationalBankAccountValidator;

final class PhpBankAccountCountryDeterminer implements BankAccountCountryDeterminer
{
    /**
     * @var iterable<NationalBankAccountValidator>
     */
    private iterable $nationalBankAccountValidators;

    /**
     * @param iterable<NationalBankAccountValidator> $nationalBankAccountValidators
     */
    public function __construct(iterable $nationalBankAccountValidators)
    {
        $this->nationalBankAccountValidators = $nationalBankAccountValidators;
    }

    public function determine(string $bankAccount): string
    {
        foreach ($this->nationalBankAccountValidators as $validator) {
            try {
                $validator->validate($bankAccount);

                return $validator->validatedCountry();
            } catch (InvalidBankAccountNumber) {
                // intentionally left blank
            }
        }

        throw UnknownBankAccountCountry::create($bankAccount);
    }
}
