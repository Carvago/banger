<?php

declare(strict_types=1);

namespace Eag\Banger\Determiner;

interface BankAccountCountryDeterminer
{
    /**
     * @throws UnknownBankAccountCountry
     */
    public function determine(string $bankAccount): string;
}
