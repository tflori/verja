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
<?php
$gate = new Verja\Gate();
$gate->addFields([
    'username' => ['notEmpty', 'strLen(3, 20)'],
    'password' => ['notEmpty', 'strLen(8)'],
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

<?php
$gate->addFields([
    'username' => (new Field())
        ->addValidator(new Validator\NotEmpty())
        ->addValidator(new Validator\StringLength(3, 20)),
    'password' => [new Validator\NotEmpty(), new Validator\StringLength(8)],
    'email' => ['notEmpty', new App\Validator\DomainEmail('my-domain.com')]
]);
```

For more information check the documentation on [github.io/verja](https://tflori.github.io/verja/). 
