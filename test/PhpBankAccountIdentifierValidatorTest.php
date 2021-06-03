<?php

declare(strict_types=1);

namespace Teas\Dms\Test\Unit\Invoice\Infrastructure\Php;

use Eag\Banger\BankAccount\BankAccount;
use Eag\Banger\Determiner\BankAccountCountryDeterminer;
use Eag\Banger\Validator\BankAccountValidator;
use Eag\Banger\Validator\NationalBankAccountIdentifierValidator\NationalBankAccountIdentifierValidator;
use Eag\Banger\Validator\PhpBankAccountIdentifierValidator;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PhpBankAccountIdentifierValidatorTest extends TestCase
{
    public function testNationalBankAccountIdentifierIsCalled(): void
    {
        $bankAccountValidator = self::createAlwaysValidBankAccountValidator();
        $bankAccountCountryDeterminer = self::createAlwaysCzechBankAccountCountryDeterminer();

        $nationalBankAccountIdentifierValidator = new class() implements NationalBankAccountIdentifierValidator {
            private bool $validateWasCalled = false;

            public function validateWasCalled(): bool
            {
                return $this->validateWasCalled;
            }

            public function validate(BankAccount $bankAccount, ?string $iban, ?string $swift): void
            {
                $this->validateWasCalled = true;
                TestCase::assertSame("CZE123", $bankAccount->number);
                TestCase::assertSame("IBAN123", $iban);
                TestCase::assertSame('SWIFT123', $swift);
            }

            public function validatedCountry(): string
            {
                return 'CZE';
            }
        };

        $validator = new PhpBankAccountIdentifierValidator([$nationalBankAccountIdentifierValidator]);

        $validator->validate(
            BankAccount::create(
                $bankAccountValidator,
                $bankAccountCountryDeterminer,
                'CZE123'
            ),
            'IBAN123',
            'SWIFT123',
        );

        self::assertTrue($nationalBankAccountIdentifierValidator->validateWasCalled());
    }

    public function testExceptionThrownOnUnknownBankAccountNumberCountry(): void
    {
        $identifierValidator = new PhpBankAccountIdentifierValidator([]);

        $countryDeterminer = self::createAlwaysCzechBankAccountCountryDeterminer();
        $bankAccountValidator = self::createAlwaysValidBankAccountValidator();

        $this->expectException(LogicException::class);
        $identifierValidator->validate(
            BankAccount::create($bankAccountValidator, $countryDeterminer, '123'),
            null,
            null,
        );
    }

    private static function createAlwaysValidBankAccountValidator(): BankAccountValidator
    {
        return new class() implements BankAccountValidator {
            public function validate(string $bankAccount, string $country): void
            {
                // intentionally left blank
            }
        };
    }

    private static function createAlwaysCzechBankAccountCountryDeterminer(): BankAccountCountryDeterminer
    {
        return new class() implements BankAccountCountryDeterminer {
            public function determine(string $bankAccount): string
            {
                return 'CZE';
            }
        };
    }
}
