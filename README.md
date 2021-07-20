# Eag/Banger

[![GitHub](https://img.shields.io/github/license/carvago/banger)](https://github.com/Carvago/banger/blob/master/LICENSE)
[![Codecov](https://img.shields.io/codecov/c/github/carvago/banger/branch/master?token=11dRr4Liln)](https://app.codecov.io/gh/carvago/banger)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/carvago/banger/Banger%20pipeline?label=pipeline)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-8892BF.svg)](https://php.net/)
![GitHub repo size](https://img.shields.io/github/repo-size/carvago/banger)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/carvago/banger)

A library for bank account validation. Now with badges!

## Introduction

Validating user inputs can be a nightmare. Bank account validation used to be
one of the worst cases since there are many formats and algorithms you have to implement. But not
anymore - say hello to Banger!

### Instalation

You can't install this library quite yet, though it will hopefully be published
to packagist and ready to install via composer soon.

```bash
composer require eag/banger # Right now you have to imagine this works.
```

### Requirements

This library requires PHP 8.0 or later and
[BCMath](http://php.net/manual/en/book.bc.php)
extension for some specific algorithms.

## Features

All features of this library were made possible via a set of classes
implementing `BankAccountValidator` interface. You can use them directly or
wrap one or more in an aggregator class.

```php
use Eag\Banger\Validator\NationalBankAccountValidator;
use Eag\Banger\Validator\NationalBankAccountValidator\CzechBankAccountValidator;

/** @var NationalBankAccountValidator $validator */
$validator = new CzechBankAccountValidator();
```

### Validating a bank account

There are two ways - either use specific national bank account validator, or
use a subset of implemented bank account validators.

#### Validate bank accounts for a single specific country

This way is perfect if you want an easy and simple solution for validating one
specific country and nothing else.

```php
use Eag\Banger\Validator\NationalBankAccountValidator;
use Eag\Banger\Validator\NationalBankAccountValidator\CzechBankAccountValidator;

/** @var NationalBankAccountValidator $validator */
$validator = new CzechBankAccountValidator();

try {
    $validator->validate('705-77628031/0710');
} catch (\Eag\Banger\Exception\InvalidBankAccountNumber){
    // Ooops, that doesn't seem to be a valid Czech bank account number
}

// We've got a valid Czech bank account number
```

#### Validate multiple bank accounts

```php
use Eag\Banger\Validator\BankAccountValidator;
use Eag\Banger\Validator\NationalBankAccountValidator;
use Eag\Banger\Validator\PhpBankAccountValidator;

/** 
 * An array of validators for each country whose bank accounts you want to validate. 
 * @var NationalBankAccountValidator[] $validators 
 */
$validators = [
    new CzechBankAccountValidator(),
    new HawaiianBankAccountValidator(),
    ...
];

/** @var BankAccountValidator $validator */
$validator = new PhpBankAccountValidator($validators);
try {
    $validator->validate('705-77628031/0710', 'CZE');
} catch (\Eag\Banger\Exception\InvalidBankAccountNumber){
    // Ooops, that doesn't seem to be a valid Czech bank account number
}

// We've got a valid Czech bank account number
```

### Determining bank accounts country

Maybe you have noticed you need to know the bank account's country to validate
it. Sometimes you already know that and want to validate a bank account for a
specific country and sometimes you don't really care that much and just want to
know it's valid - `BankAccountCountryDeterminer` to the rescue!

#### Determine bank account country

A determiner is your friend when you want to know the country of a bank account.

```php
use Eag\Banger\Determiner\BankAccountCountryDeterminer;
use Eag\Banger\Determiner\PhpBankAccountCountryDeterminer;
use Eag\Banger\Validator\NationalBankAccountValidator;

/** 
 * An array of validators for each country you wish to determine. 
 * @var NationalBankAccountValidator[] $validators 
 */
$validators = [
    new CzechBankAccountValidator(),
    new HawaiianBankAccountValidator()
];

/** @var BankAccountCountryDeterminer $determiner */
$determiner = new PhpBankAccountCountryDeterminer($validators);

echo $determiner->determine('705-77628031/0710'); // CZE
echo $determiner->determine('123431'); // throws UnknownBankAccountCountry exception
```

#### Validate bank account from unknown country

Combine with a `BankAccountValidator` to validate bank account whose country you don't know.

```php
use Eag\Banger\Determiner\BankAccountCountryDeterminer;
use Eag\Banger\Determiner\PhpBankAccountCountryDeterminer;
use Eag\Banger\Validator\BankAccountValidator;
use Eag\Banger\Validator\PhpBankAccountValidator;

$bankAccountNumber = '705-77628031/0710';

$validators = [...];
/** @var BankAccountCountryDeterminer $determiner */
$determiner = new PhpBankAccountCountryDeterminer($validators);
/** @var BankAccountValidator $validator */
$validator = new PhpBankAccountValidator($validators);

try {
    $validator->validate($bankAccountNumber, $determiner->determine($bankAccountNumber));
} catch (\Eag\Banger\Exception\InvalidBankAccountNumber){
    // Ooops, that doesn't seem to be a valid bank account number
}

// We've got a valid bank account number
```

### FAQ

 > Why don't you just give me a single simple class that validates all bank accounts?

There are two main reasons:

1) This way you can use your own implementations of
   `NationalBankAccountValidator` when it is not implemented yet or the current
   implementation doesn't meet your expectations. 
2) We didn't implement all validators just yet. Every time we would add a new
   validator we would also introduce a breaking change to existing codebases.
