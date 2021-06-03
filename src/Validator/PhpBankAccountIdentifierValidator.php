<?php

declare(strict_types=1);

namespace Eag\Banger\Validator;

use Eag\Banger\BankAccount\BankAccount;
use Eag\Banger\Exception\InvalidBankAccountIdentifier;
use Eag\Banger\Validator\NationalBankAccountIdentifierValidator\NationalBankAccountIdentifierValidator;
use LogicException;

final class PhpBankAccountIdentifierValidator implements BankAccountIdentifierValidator
{
    /**
     * @var array<NationalBankAccountIdentifierValidator>
     */
    private array $nationalBankAccountIdentifierValidators = [];

    /**
     * @param iterable<NationalBankAccountIdentifierValidator> $nationalBankAccountIdentifierValidators
     */
    public function __construct(iterable $nationalBankAccountIdentifierValidators)
    {
        foreach ($nationalBankAccountIdentifierValidators as $validator) {
            $countryCode = $validator->validatedCountry();
            $this->nationalBankAccountIdentifierValidators[$countryCode] = $validator;
        }
    }

    /**
     * @throws InvalidBankAccountIdentifier
     */
    public function validate(BankAccount $bankAccount, ?string $iban, ?string $swift): void
    {
        $alpha3Code = $bankAccount->country;
        if (!isset($this->nationalBankAccountIdentifierValidators[$alpha3Code])) {
            throw new LogicException("Missing NationalBankAccountIdentifierValidator for country {$alpha3Code}");
        }

        $validator = $this->nationalBankAccountIdentifierValidators[$alpha3Code];
        $validator->validate($bankAccount, $iban, $swift);
    }
}
