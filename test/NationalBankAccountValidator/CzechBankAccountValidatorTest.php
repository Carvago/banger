<?php

declare(strict_types=1);

namespace Eag\Banger\Test\NationalBankAccountValidator;

use Eag\Banger\Exception\InvalidBankAccountNumber;
use Eag\Banger\Validator\NationalBankAccountValidator\CzechBankAccountValidator;
use PHPUnit\Framework\TestCase;

final class CzechBankAccountValidatorTest extends TestCase
{
    /**
     * @dataProvider validBankAccountNumbersDataset
     * @doesNotPerformAssertions
     */
    public function testValidBankAccountNumber(string $bankAccountNumber): void
    {
        $validator = new CzechBankAccountValidator();
        $validator->validate($bankAccountNumber);
    }

    /**
     * @dataProvider invalidBankAccountNumbersDataset
     */
    public function testExceptionIsThrownOnInvalidBankAccountNumber(string $bankAccountNumber): void
    {
        $validator = new CzechBankAccountValidator();

        $this->expectException(InvalidBankAccountNumber::class);
        $validator->validate($bankAccountNumber);
    }

    /**
     * @return iterable<array<string>>
     */
    public function validBankAccountNumbersDataset(): iterable
    {
        yield "Czech Red Cross" => ['333999/2700'];
        yield "Centrum Paraple" => ['932932932/0300'];
        yield "Linka bezpečí" => ['3856680/0300'];
        yield "Czech Tax service - Beer tax" => ['4773-7622021/0710'];
    }

    /**
     * @return iterable<array<string>>
     */
    public function invalidBankAccountNumbersDataset(): iterable
    {
        yield "Missing bank code" => ['333999'];
        yield "Bank code too long" => ['932932932/03001'];
        yield "Bank code too short" => ['932932932/030'];
        yield "Account number too short" => ['12/0300'];
        yield "Account number too long" => ['12345678901/0300'];
        yield "Prefix too short" => ['-7622021/0710'];
        yield "Prefix too long" => ['1234567-7622021/0710'];
        yield "Unknown bank code" => ['333999/9999'];
        yield "Checksum not valid" => ['333998/2700'];
        yield "Just some random rubbish" => ['273921739216396219369216392163921639'];
    }
}
