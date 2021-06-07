<?php

declare(strict_types=1);

namespace Eag\Banger\Validator\NationalBankAccountIdentifierValidator;

use Eag\Banger\BankAccount\BankAccount;
use Eag\Banger\Exception\InvalidBankAccountIdentifierIban;
use Eag\Banger\Exception\InvalidBankAccountIdentifierSwift;
use LogicException;
use Safe\Exceptions\PcreException;
use Safe\Exceptions\StringsException;

use function Safe\preg_match;
use function Safe\substr;

final class CzechBankAccountIdentifierValidator implements NationalBankAccountIdentifierValidator
{
    private const BANK_TO_SWIFT = [
        "0100" => "KOMBCZPP",
        "0300" => "CEKOCZPP",
        "0600" => "AGBACZPP",
        "0710" => "CNBACZPP",
        "0800" => "GIBACZPX",
        "2010" => "FIOBCZPP",
        "2020" => "BOTKCZPP",
        "2030" => null,
        "2060" => "CITFCZPP",
        "2070" => "MPUBCZPP",
        "2100" => null,
        "2200" => null,
        "2220" => "ARTTCZPP",
        "2240" => "POBNCZPP",
        "2250" => "CTASCZ22",
        "2260" => null,
        "2275" => null,
        "2600" => "CITICZPX",
        "2700" => "BACXCZPP",
        "3030" => "AIRACZPP",
        "3050" => "BPPFCZP1",
        "3060" => "BPKOCZPP",
        "3500" => "INGBCZPP",
        "4000" => "EXPNCZPP",
        "4300" => "CMZRCZP1",
        "5500" => "RZBCCZPP",
        "5800" => "JTBPCZPP",
        "6000" => "PMBPCZPP",
        "6100" => "EQBKCZPP",
        "6200" => "COBACZPX",
        "6210" => "BREXCZPP",
        "6300" => "GEBACZPP",
        "6700" => "SUBACZPP",
        "6800" => "VBOECZ2X",
        "7910" => "DEUTCZPX",
        "7940" => "SPWTCZ21",
        "7950" => null,
        "7960" => null,
        "7970" => null,
        "7980" => null,
        "7990" => null,
        "8030" => "GENOCZ21",
        "8040" => "OBKLCZ2X",
        "8060" => null,
        "8090" => "CZEECZPP",
        "8150" => "MIDLCZPP",
        "8190" => null,
        "8198" => "FFCSCZP1",
        "8199" => "MOUSCZP2",
        "8200" => null,
        "8215" => null,
        "8220" => "PAERCZP1",
        "8230" => null,
        "8240" => null,
        "8250" => "BKCHCZPP",
        "8255" => "COMMCZPP",
        "8260" => "PYYMCZPP",
        "8265" => "ICBKCZPP",
        "8270" => "FAPOCZP1",
        "8272" => "VPAYCZP2",
        "8280" => "BEFKCZP1",
        "8283" => "QPSRCZPP",
        "8292" => null,
        "8293" => "MRPSCZPP",
        "8294" => null,
    ];

    /**
     * @throws InvalidBankAccountIdentifierIban
     * @throws InvalidBankAccountIdentifierSwift
     */
    public function validate(BankAccount $bankAccount, ?string $iban, ?string $swift): void
    {
        if ($iban !== null) {
            if (!self::isValidIban($iban)) {
                throw InvalidBankAccountIdentifierIban::create($bankAccount, $iban);
            }

            try {
                $ibanBankAccountNumber = self::extractBankAccountNumberFromIban($iban);
            } catch (StringsException) {
                throw InvalidBankAccountIdentifierIban::create($bankAccount, $iban);
            }

            if (self::normalizeBankAccountNumber($bankAccount->number) !== self::normalizeBankAccountNumber(
                $ibanBankAccountNumber
            )) {
                throw InvalidBankAccountIdentifierIban::create($bankAccount, $iban);
            }
        }

        if ($swift !== null) {
            try {
                $bankCode = substr($bankAccount->number, -4);
            } catch (StringsException $e) {
                throw InvalidBankAccountIdentifierSwift::create($bankAccount, $swift);
            }

            $expectedSwift = self::BANK_TO_SWIFT[$bankCode];
            if ($expectedSwift !== $swift) {
                throw InvalidBankAccountIdentifierSwift::create($bankAccount, $swift);
            }
        }
    }

    public function validatedCountry(): string
    {
        return 'CZ';
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    private static function checksum(string $iban): int
    {
        $iban = substr($iban, 4) . substr($iban, 0, 2) . '00';
        $digits = array_map(
            fn ($c) => ctype_alpha($c) ? (string)(ord($c) - ord('A') + 10) : $c,
            str_split($iban)
        );

        $oneBigNumber = ltrim(implode('', $digits), '0');

        return 98 - (int)bcmod($oneBigNumber, '97');
    }

    private static function isValidIban(string $iban): bool
    {
        try {
            if (preg_match('/^CZ(\d{2})\d{20}$/', $iban, $matches) !== 1) {
                return false;
            }

            $ibanChecksum = (int)ltrim($matches[1], '0');
            $calculatedChecksum = self::checksum($iban);
        } catch (StringsException | PcreException $e) {
            return false;
        }

        return $ibanChecksum === $calculatedChecksum;
    }

    private static function normalizeBankAccountNumber(string $bankAccountNumber): string
    {
        if (preg_match('/^(?:(\d+)-)?(\d+)\/(\d+)$/', $bankAccountNumber, $matches) !== 1) {
            throw new LogicException("Expecting valid Czech bank account number");
        }

        [, $prefix, $number, $bankCode] = $matches;

        $prefix = ltrim($prefix, '0');
        $number = ltrim($number, '0');

        if ($prefix === '') {
            return "{$number}/{$bankCode}";
        } else {
            return "{$prefix}-{$bankCode}/{$bankCode}";
        }
    }

    /**
     * @throws \Safe\Exceptions\StringsException
     */
    private static function extractBankAccountNumberFromIban(string $iban): string
    {
        $ibanBankCode = substr($iban, 4, 4);
        $ibanAccountNumberPrefix = substr($iban, 8, 6);
        $ibanAccountNumber = substr($iban, 14, 10);

        if ($ibanAccountNumberPrefix === '') {
            return "{$ibanAccountNumber}/{$ibanBankCode}";
        }

        return "{$ibanAccountNumberPrefix}-{$ibanAccountNumber}/{$ibanBankCode}";
    }
}
