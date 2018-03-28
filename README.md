# tflori/verja

[![Build Status](https://travis-ci.org/tflori/verja.svg?branch=master)](https://travis-ci.org/tflori/verja)
[![Coverage Status](https://coveralls.io/repos/github/tflori/verja/badge.svg?branch=master)](https://coveralls.io/github/tflori/verja?branch=master)
[![Latest Stable Version](https://poser.pugx.org/tflori/verja/v/stable.svg)](https://packagist.org/packages/tflori/verja) 
[![Total Downloads](https://poser.pugx.org/tflori/verja/downloads.svg)](https://packagist.org/packages/tflori/verja) 
[![License](https://poser.pugx.org/tflori/verja/license.svg)](https://packagist.org/packages/tflori/verja)

TL;DR An validation tool for arrays filled by foreign input like forms, json data, query parameters etc. The name is
from Old Norse language and means defender.

## Installation

The usual way...

```console
composer require tflori/verja
```

## Usage

Initialize a container, set the input data, define filters and validators, validate the data, get the data.

```php
$gate = new Verja\Gate();
$gate->accepts([
    'username' => ['notEmpty', 'strLen:3:20'],
    'password' => ['notEmpty', 'strLen:8'],
    'email' => ['notEmpty', 'email'],
]);

if ($gate->validate($_POST)) {
  // how ever your orm works..
  $user = new User($gate->getData());
  $user->save();
} else {
  $errors = $gate->getErrors();
}
```

If you prefer auto completion you can of course pass objects:

```php
use Verja\Validator;

$gate->accepts([
    'username' => (new Field())
        ->addValidator(new Validator\NotEmpty())
        ->addValidator(new Validator\StrLen(3, 20)),
    'password' => [new Validator\NotEmpty(), new Validator\StrLen(8)],
    'email' => ['notEmpty', new App\Validator\DomainEmail('my-domain.com')]
]);
```

For more information check the documentation on [github.io/verja](https://tflori.github.io/verja/). 

## Predefined Validators

In this library the following validators are included:

- `After`: Value must be a date time after `$dateTime`
- `Alpha`: Value must contain only alphabetical characters
- `Before`: Value must be a date time before `$dateTime`
- `Boolean`: Value must be boolean
- `Between`: Value must be between `$min` and `$max`
- `Contains`: Value must contain `$subString`
- `CreditCard`: Value must be a valid credit card number
- `DateTime`: Value must be a valid date in `$format`
- `EmailAddress`: Value must be a valid email address
- `Equals`: Field must match field `$opposide`
- `InArray`: Value must exist in `$array`
- `Integer`: Value must be integer
- `IpAddress`: Value must be a valid IP address of `$version`
- `IsArray`: Value must be an array
- `NotEmpty`: Value must not be empty
- `Numeric`: Value must be numeric
- `PregMatch`: Value must match regular expression `$pattern`
- `StrLen`: String length from value must be between `$min` and `$max`
- `Truthful`: Converted to boolean the value must be true

Planned:

- `Url`: Value must be a valid URL
- `AlphaNumeric`: Value must contain only alphabetic and numeric characters
- `Slug`: Value must contain only slug characters (a-z, 0-9, -, _)

# Predefined Filters

The following filters are included in this library:

- `Boolean`: Converts integer and string values to boolean
- `ConvertCase`: Converts case to `$mode` (upper, lower or title)
- `DateTime`: Converts string from `$format` in `DateTime` object
- `Escape`: Escape special characters for usage in html
- `Integer`: Converts string values to integer
- `Numeric`: Converts string values to float or integer
- `Replace`: Replaces `$search` in values with `$replace`
- `Trim`: Trims `$charcterMask` from values

Planned:

- `PregReplace`: Replaces `$pattern` with `$replace` (replace can also be a callback)
