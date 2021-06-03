<?php

declare(strict_types=1);

namespace Teas\Dms\Test\Unit\Invoice\Infrastructure\Php;

use Eag\Banger\Validator\NationalBankAccountValidator;
use Eag\Banger\Validator\PhpBankAccountValidator;
use LogicException;
use PHPUnit\Framework\TestCase;

final class PhpBankAccountValidatorTest extends TestCase
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
                return 'CZE';
            }
        };

        $validator = new PhpBankAccountValidator([$nationalBankAccountValidator]);
        $validator->validate('CZE123', 'CZE');

        self::assertTrue($nationalBankAccountValidator->validateWasCalled());
    }

    public function testExceptionThrownOnUnknownBankAccountNumberCountry(): void
    {
        $validator = new PhpBankAccountValidator([]);

        $this->expectException(LogicException::class);
        $validator->validate('123', 'CZE');
    }
}
