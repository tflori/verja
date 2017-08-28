---
layout: default
title: Introduction
permalink: /index.html
---
## {{ page.title }}

*Verja* is a very simple and stupid library to filter and validate input data. The name *Verja* comes from the Old
Norse language and means defender. The idea behind this name is that the library defends you from invalid input.

The interface is very straight forward. `Verja\Gate` is the main object (you should **not reuse** this object). It
holds the data that should be validated, and the `Verja\Field`s. Each field has it's own filters and validators. When
you run `$container->validate()` each field gets filtered and validated.

Here is a small pseudo code example to explain the simplicity of this library:

```php?start_inline=true
$rawData = ['username' => 'any username'];
$fields = [
    'username' => (new Verja\Field())
        ->addFilter(new Verja\Filter\Trim)
        ->addValidator(new Verja\Validator\NotEmpty)
];
foreach ($fields as $key => $field) {
    $field->validate($field->filter($rawData[$key]));
}
```

Not more, not less.  
