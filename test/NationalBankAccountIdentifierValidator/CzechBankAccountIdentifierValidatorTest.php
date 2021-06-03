<?php

declare(strict_types=1);

namespace Eag\Banger\Test\NationalBankAccountIdentifierValidator;

use Eag\Banger\BankAccount\BankAccount;
use Eag\Banger\Determiner\BankAccountCountryDeterminer;
use Eag\Banger\Exception\InvalidBankAccountIdentifierIban;
use Eag\Banger\Exception\InvalidBankAccountIdentifierSwift;
use Eag\Banger\Validator\BankAccountValidator;
use Eag\Banger\Validator\NationalBankAccountIdentifierValidator\CzechBankAccountIdentifierValidator;
use PHPUnit\Framework\TestCase;

final class CzechBankAccountIdentifierValidatorTest extends TestCase
{
    /**
     * @dataProvider validBankAccountIdentifiersDataset
     * @doesNotPerformAssertions
     */
    public function testValidBankAccountIdentifier(BankAccount $bankAccount, ?string $iban, ?string $swift): void
    {
        $validator = new CzechBankAccountIdentifierValidator();
        $validator->validate($bankAccount, $iban, $swift);
    }

    /**
     * @dataProvider invalidIbanBankAccountIdentifiersDataset
     */
    public function testExceptionIsThrownOnInvalidIban(BankAccount $bankAccount, ?string $iban, ?string $swift): void
    {
        $validator = new CzechBankAccountIdentifierValidator();

        $this->expectException(InvalidBankAccountIdentifierIban::class);
        $validator->validate($bankAccount, $iban, $swift);
    }

    /**
     * @dataProvider invalidSwiftBankAccountIdentifiersDataset
     */
    public function testExceptionIsThrownOnInvalidSwift(BankAccount $bankAccount, ?string $iban, ?string $swift): void
    {
        $validator = new CzechBankAccountIdentifierValidator();

        $this->expectException(InvalidBankAccountIdentifierSwift::class);
        $validator->validate($bankAccount, $iban, $swift);
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function validBankAccountIdentifiersDataset(): iterable
    {
        $countryDeterminer = self::createAlwaysCzechBankAccountCountryDeterminer();
        $validator = self::createAlwaysValidBankAccountValidator();

        yield "Czech Red Cross" => [
            BankAccount::create($validator, $countryDeterminer, '333999/2700'),
            'CZ1427000000000000333999',
            'BACXCZPP',
        ];
        yield "Czech Red Cross (without IBAN & SWIFT)" => [
            BankAccount::create($validator, $countryDeterminer, '333999/2700'),
            null,
            null,
        ];
        yield "Centrum Paraple" => [
            BankAccount::create($validator, $countryDeterminer, '932932932/0300'),
            'CZ9503000000000932932932',
            'CEKOCZPP',
        ];
        yield "Linka bezpečí" => [
            BankAccount::create($validator, $countryDeterminer, '3856680/0300'),
            'CZ6103000000000003856680',
            'CEKOCZPP',
        ];
        yield "Czech Tax service - Beer tax" => [
            BankAccount::create($validator, $countryDeterminer, '4773-7622021/0710'),
            'CZ5507100047730007622021',
            'CNBACZPP',
        ];
        yield "TEAS (with leading zeroes)" => [
            BankAccount::create($validator, $countryDeterminer, '000123-0123150287/0100'),
            'CZ5401000001230123150287',
            'KOMBCZPP',
        ];
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function invalidSwiftBankAccountIdentifiersDataset(): iterable
    {
        $countryDeterminer = self::createAlwaysCzechBankAccountCountryDeterminer();
        $validator = self::createAlwaysValidBankAccountValidator();

        yield "SWIFT code doesn't match bank" => [
            BankAccount::create($validator, $countryDeterminer, '333999/2700'),
            'CZ1427000000000000333999',
            'AACXCZPP',
        ];
        yield "SWIFT code is just plain out wrong" => [
            BankAccount::create($validator, $countryDeterminer, '333999/2700'),
            'CZ1427000000000000333999',
            'QWERTYUIOPASDSFSFSDFZCZCXZC',
        ];
    }

    /**
     * @return iterable<array<mixed>>
     */
    public function invalidIbanBankAccountIdentifiersDataset(): iterable
    {
        $countryDeterminer = self::createAlwaysCzechBankAccountCountryDeterminer();
        $validator = self::createAlwaysValidBankAccountValidator();

        yield "BankAccount number doesn't match IBAN bank account number" => [
            BankAccount::create($validator, $countryDeterminer, '333998/2700'),
            'CZ1427000000000000333999',
            'BACXCZPP',
        ];
        yield "Miscalculated IBAN checksum" => [
            BankAccount::create($validator, $countryDeterminer, '333998/2700'),
            'CZ1527000000000000333999',
            'BACXCZPP',
        ];
        yield "Wrong IBAN format (too long)" => [
            BankAccount::create($validator, $countryDeterminer, '333998/2700'),
            'CZ152700000000000033399999',
            'AGBACZPP',
        ];
        yield "Wrong IBAN format (wrong country)" => [
            BankAccount::create($validator, $countryDeterminer, '333999/2700'),
            'SK1427000000000000333999',
            'BACXCZPP',
        ];
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
                return 'CZ';
            }
        };
    }
}
