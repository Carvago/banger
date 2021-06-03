<?php

declare(strict_types=1);

namespace Eag\Banger\Validator\NationalBankAccountValidator;

use Eag\Banger\Exception\InvalidBankAccountNumber;
use Eag\Banger\Validator\NationalBankAccountValidator;

use function Safe\preg_match;

final class CzechBankAccountValidator implements NationalBankAccountValidator
{
    private const BANK_CODES = [
        "0100" => 1,
        "0300" => 1,
        "0600" => 1,
        "0710" => 1,
        "0800" => 1,
        "2010" => 1,
        "2020" => 1,
        "2030" => 1,
        "2060" => 1,
        "2070" => 1,
        "2100" => 1,
        "2200" => 1,
        "2220" => 1,
        "2240" => 1,
        "2250" => 1,
        "2260" => 1,
        "2275" => 1,
        "2600" => 1,
        "2700" => 1,
        "3030" => 1,
        "3050" => 1,
        "3060" => 1,
        "3500" => 1,
        "4000" => 1,
        "4300" => 1,
        "5500" => 1,
        "5800" => 1,
        "6000" => 1,
        "6100" => 1,
        "6200" => 1,
        "6210" => 1,
        "6300" => 1,
        "6700" => 1,
        "6800" => 1,
        "7910" => 1,
        "7940" => 1,
        "7950" => 1,
        "7960" => 1,
        "7970" => 1,
        "7980" => 1,
        "7990" => 1,
        "8030" => 1,
        "8040" => 1,
        "8060" => 1,
        "8090" => 1,
        "8150" => 1,
        "8190" => 1,
        "8198" => 1,
        "8199" => 1,
        "8200" => 1,
        "8215" => 1,
        "8220" => 1,
        "8230" => 1,
        "8240" => 1,
        "8250" => 1,
        "8255" => 1,
        "8260" => 1,
        "8265" => 1,
        "8270" => 1,
        "8272" => 1,
        "8280" => 1,
        "8283" => 1,
        "8292" => 1,
        "8293" => 1,
        "8294" => 1,
    ];

    public function validate(string $bankAccount): void
    {
        if (preg_match('/^(?:([0-9]{1,6})-)?([0-9]{2,10})\/([0-9]{4})$/', $bankAccount, $matches) !== 1) {
            throw InvalidBankAccountNumber::create($bankAccount, $this->validatedCountry());
        }

        [, $prefix, $number, $bank] = $matches;

        if (!array_key_exists($bank, self::BANK_CODES)) {
            throw InvalidBankAccountNumber::create($bankAccount, $this->validatedCountry());
        }

        if (self::checksum($prefix) % 11 !== 0 || self::checksum($number) % 11 !== 0) {
            throw InvalidBankAccountNumber::create($bankAccount, $this->validatedCountry());
        }
    }

    public function validatedCountry(): string
    {
        return 'CZE';
    }

    private static function checksum(string $input): int
    {
        $input = str_pad($input, 10, '0', STR_PAD_LEFT);
        $weights = [6, 3, 7, 9, 10, 5, 8, 4, 2, 1];
        $digits = array_map('intval', str_split($input));

        return array_sum(array_map(fn(int $weight, int $digit): int => $weight * $digit, $weights, $digits));
    }
}
