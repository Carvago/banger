<?php

declare(strict_types=1);

namespace Eag\Banger\Validator;

use LogicException;

final class PhpBankAccountValidator implements BankAccountValidator
{
    /**
     * @var array<NationalBankAccountValidator>
     */
    private array $nationalBankAccountValidators = [];

    /**
     * @param iterable<NationalBankAccountValidator> $nationalBankAccountValidators
     */
    public function __construct(iterable $nationalBankAccountValidators)
    {
        foreach ($nationalBankAccountValidators as $validator) {
            $this->nationalBankAccountValidators[$validator->validatedCountry()] = $validator;
        }
    }

    public function validate(string $bankAccount, string $country): void
    {
        // TODO: Probably should not be a logic exception
        $validator = $this->nationalBankAccountValidators[$country] ?? throw new LogicException(
                "Missing NationalBankAccountValidator for country {$country}"
            );

        $validator->validate($bankAccount);
    }
}
