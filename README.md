# tflori/verja

[![Build Status](https://travis-ci.org/tfloir/verja.svg?branch=master)](https://travis-ci.org/tfloir/verja)
[![Coverage Status](https://coveralls.io/repos/github/tfloir/verja/badge.svg?branch=master)](https://coveralls.io/github/tfloir/verja?branch=master)
[![Latest Stable Version](https://poser.pugx.org/tfloir/verja/v/stable.svg)](https://packagist.org/packages/tfloir/verja) 
[![Total Downloads](https://poser.pugx.org/tfloir/verja/downloads.svg)](https://packagist.org/packages/tfloir/verja) 
[![License](https://poser.pugx.org/tfloir/verja/license.svg)](https://packagist.org/packages/tfloir/verja)

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
$container = new Verja\Container();
$container->setData($_POST);
$container->addFields([
    'username' => ['notEmpty', 'strLen(3, 20)'],
    'password' => ['notEmpty', 'strLen(8)'],
    'email' => ['notEmpty', 'email'],
]);

if ($container->validate()) {
  // how ever your orm works..
  $user = new User($container->getData());
  $user->save();
} else {
  $errors = $container->getErrors();
}
```

If you prefer auto completion you can of course pass objects:

```php
$container->addFields([
    'username' => (new Field())
        ->addValidator(new Validator\NotEmpty())
        ->addValidator(new Validator\StringLength(3, 20)),
    'password' => [new Validator\NotEmpty(), new Validator\StringLength(8)],
    'email' => ['notEmpty', new App\Validator\DomainEmail('my-domain.com')]
]);
```

For more information check the documentation on [github.io/verja](https://tflori.github.io/verja/). 
