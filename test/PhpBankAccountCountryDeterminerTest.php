<?php

declare(strict_types=1);

namespace Teas\Dms\Test\Unit\Invoice\Infrastructure\Php;

use Eag\Banger\Determiner\PhpBankAccountCountryDeterminer;
use Eag\Banger\Determiner\UnknownBankAccountCountry;
use Eag\Banger\Validator\NationalBankAccountValidator;
use PHPUnit\Framework\TestCase;

final class PhpBankAccountCountryDeterminerTest extends TestCase
{
    public function testNationalBankAccountValidatorIsCalled(): void
    {
        $nationalBankAccountValidator = new class() implements NationalBankAccountValidator {
            private bool $validateWasCalled = false;

            public function validateWasCalled(): bool
            {
                return $this->validateWasCalled;
            }

            public function validate(string $bankAccount): void
            {
                $this->validateWasCalled = true;
                TestCase::assertSame("CZE123", $bankAccount);
            }

            public function validatedCountry(): string
            {
                return 'CZ';
            }
        };

        $determiner = new PhpBankAccountCountryDeterminer([$nationalBankAccountValidator]);
        $determiner->determine('CZE123');

        self::assertTrue($nationalBankAccountValidator->validateWasCalled());
    }

    public function testExceptionThrownOnUnknownBankAccountNumberCountry(): void
    {
        $determiner = new PhpBankAccountCountryDeterminer([]);

        $this->expectException(UnknownBankAccountCountry::class);
        $determiner->determine('123');
    }
}
