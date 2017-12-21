---
layout: default
title: Differences to other validation libraries
permalink: /differences.html
---
## {{ page.title }}

There are a lot of libraries for validation. The question is: why to use this one? I began with this library because
none of the existing was what I was looking for. Ok, of course I did not check all libraries - there are too many.

Here I want to show 3 libraries with typical code and why I prefer verja over them. To use everywhere the same code
here is the example using Verja:

```php
<?php

$gate = new Verja\Gate();
$gate->accepts([
    'name' => ['required', 'strLen:3:20'],
    'age' => ['integer'],
]);

if ($gate->validate($_POST)) {
    echo 'Please welcome ' . $gate->get('name') . '!';
    if ($gate->get('age') === 18) { // note: this is a type safe check 
        echo 'He just get 18 and wants to be a millionaire.';
    } elseif ($gate->get('age') === null) {
        echo 'He does not want to tell us his age.';
    } else {
        echo 'He is already ' . $gate->get('age') . '.';
    }
} else {
    echo 'Errors during registration:' . PHP_EOL;
    foreach ($gate->getErrors() as $field => $errors) {
        echo 'Invalid ' . $field . ': ' . implode('; ', array_map(function(Verja\Error $error) {
            return $error->message;
        }, $errors));
    }
}
```

### respect/validation

### vlucas/valitron

Valitron is also an easy to use validator without any dependencies (except php >= 5.3.2). It has about 320k downloads,
exists since February 2013 and is still actively maintained.

**Example:**

```php
<?php

$v = Valitron\Validator($_POST);
$v->rule('required', 'name');
$v->rule('lengthBetween', 'name', 3, 20);
$v->rule('integer', 'age');

if ($v->validate()) {
    echo 'Please welcome ' . $_POST['name'] . '!';
    if (!empty($_POST['age']) && (int)$_POST['age'] === 18) { 
        echo 'He just get 18 and wants to be a millionaire.';
    } elseif (empty($_POST['age'])) {
        echo 'He does not want to tell us his age.';
    } else {
        echo 'He is already ' . $_POST['age'] . '.';
    }
} else {
    echo 'Errors during registration:' . PHP_EOL;
    foreach ($v->errors() as $field => $errors) {
        echo 'Invalid ' . $field . ': ' . implode('; ', $errors);
    }
}
```

**Round-Up:**

- It's even more simple from the first view but notice that you don't get the values from the validation (you can get
  what you input but it's 1:1 the same).
- The errors are already translated but you have to provide the translations in the syntax the library expects and they
  are translated even if you don't want to show them. You can also translate the messages your self (for example the
  message for age could be `'Age must be an integer'`).
- You have to provide the array to validate to the constructor. When you later call `$v->withData()` a clone will be
  generated - I'm unsure if this is testable.

**Pros:**

- Simple to use
- A lot of validations out of the box
- Custom validations by closures with names

**Cons:**
- Hard to test
- Custom validations have to be given through static `addRule` method
- No error keys and parameters
- No filtering
- Documentation only in readme and not as descriptive as needed
- No auto completion possible for validation rules
- Unusual namings of methods `$v->mapFieldsRules($rules);` is similar to `addFields()`
- Messages have to be given after the validator
- Unclear source code in a huge one for all class

### wixel/gump
