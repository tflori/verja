---
layout: default
title: Usage
permalink: /usage.html
---
## {{ page.title }}

An important factor of a library is the interface. We can keep all information on one page because it's really straight
froward.

### Initialize

Usually you need validation and filtering in controllers where you control the application flow and pass user (or third
party) input to your business models. To test these controllers there are now two options:

1. Test with edge cases (a lot of different input value combinations)
2. Test that the expected validators get created and used.

We suggest the second method. So put your `new Verja\Gate()` to a factory and replace this factory to mock this object
in tests.

```php?start_inline=true
use DependencyInjector\DI;
use Verja\Gate;

DI::set('verja', function() {
    return new Gate();
}, false);

function controller() {
    /** @var Gate $gate */
    $gate = DI::get('verja');
}
```

> Note: we are using `tflori\dependency-injector` here. The third parameter means that the object should not be cached.
> Instead every time a new `Gate` will be initialized. 

### Define Fields

A `Field` can be initialized as a usual object and passed to `Gate` with `Gate::addField()`. You can also pass an
array of fields to `Gate::addFields()`. These methods are literally aliased by `Gate::accept()` and `Gate::accepts()`.

> Note: The `Gate` will not let any input pass that is not allowed to pass. That means that you also have to define
> fields that you don't want to validate or filter.

These methods also allow the short form of defining fields to write code much easier and faster. So the following
calls are equal and allow any value for the key `'comment'`:

```php?start_inline=true
use Verja\Gate;
use Verja\Field;

$gate = new Gate();

$gate->accept('comment');
$gate->accept('comment', []);
$gate->accept('comment', new Field());

$gate->accepts(['comment']);
$gate->accepts(['comment' => []]);
$gate->accepts(['comment' => new Field()]);
```

> Note: the later calls just overwrite the comment definition.

#### Add Filters To Field

You can add any object of a class that is implementing `Verja\FilterInterface` to a `Field` with `Field::addFilter()`.
Because the order might be significant you can also prepend a filter with either `Field::prependFilter()` or the
parameter `$prepend` set to `true`.

The methods to add a filter to an field also allow `$filter` to be a string or a callable. To pass a callable is a 
shortcut for passing `new \Verja\Filter\Callback($callable)`. A string will be converted to a filter with
`\Verja\Filter::fromString()` which is searching in LIFO order (last in first out) in all registered namespaces for a
matching filter and colons divide the parameters.

Filters can also be passed to the constructor of `Field` in an array where the order is maintained.

```php?start_inline=true
use Verja\Field;
use Verja\Filter;

$field = new Field([new Filter\Trim('/')]); // trim slashes
$field->prependFilter('trim'); // first trim whitespace
$field->appendFilter('replace:foo:bar'); // after trim whitespace and trim slashes replace foo with bar
$field->addFilter(function ($value) {
    return substr($value, 0, 3); // get the first 3 chars of the result
});
```

#### Add Validators To Field

The identical methods exists for the `Verja\ValidatorInterface`. The order is also maintained and is relevant when you
try to get value from a field that is not valid. In this case the error message of the first validator that fails is
in the exception message.

The converter method `\Verja\Validator::fromString()` allows an exclamation mark in front of the validator name that
will invert the validator.

```php?start_inline=true
use Verja\Field;
use Verja\Validator;
use DependencyInjector\DI;

$field = new Field([new Validator\Contains('@')]); // validate that it contains an @
$field->prependValidator('notEmpty'); // prepend not empty check
$field->appendValidator('emailAddress'); // append that it is an email address
$field->addValidator(function ($value) { // validate that the email is unknown
    if (DI::get('db')->select('user')->where('email', $value)->count()) {
        return Validator::buildError('EMAIL_TAKEN', $value, 'Email address already taken');
    }
    return true;
});
```

#### Type Validation And Filtering

For validating types (integer, boolean or even classes) often it is required to validate first and then filter. For
example a numeric value: `$number = is_numeric($_POST['alpha']) ? (double)$_POST['alpha'] : 1;`. To allow this a filter
can throw a `InvalidValue` exception or use the defined method `validate` to do so. This method executes the validator
and throws if the value is not valid. The exception will be caught and no other filters or validators are executed 
because the value could not be filtered.

For example you could write a filter for getting a `User` object:

```php?start_inline=true
use Verja\Filter;
use Verja\Gate;
use App\User; // a user entity class

class UserFilter extends Filter {
    public function filter($value, array $context = []) {
        Gate::assert('integer', $value); // this ensures the $value is an integer
        return User::findOrFail($value);
    }
}
```

#### Required Fields

Fields can be required which means that an error leads to an exception when you try to get data for this field and the
value is validated even if it is empty. On the other hand it means that a required field is ok to be empty if no
validator is defined.

The required attribute can be set via `Field::required()` or the string `'required'` in the array of definitions.

```php?start_inline=true
use Verja\Field;

$field = new Field([ 'required', 'notEmpty' ]);
```

#### Alternative declaration

Filters and validators can be defined in three different ways:

1. As string to `new Field()` or explicit `Filter::fromString()` and `Validator::fromString()`
2. With keyword `new` directly
3. With magic `__callStatic()` method

The shortest way to write is as string - however it does not allow auto completion and can not be intuitive. Especially
new developers will find it hard to find out how the filter or validator is called. Also you might be wondering when
defining a new field and get a filter instead of a validator what happens when they have the same name.

The most understandable way is to create the objects by yourself - it's also the fastest script as it is the most strait
forward way. But you can't call directly method on the object. Instead you have to surround it with parenthesis and so
on. Also it is the the longest way to write.

Another approach are the magic methods. We can type hint these and then they allow auto completion. But it's harder to
get auto completion for custom validators and filters.

Here is a very short example with all three methods:

```php?start_inline=true
use Verja\Field;
use Verja\Validator;
use Verja\Filter;

// use strings
$field = new Field(['trim', 'notEmpty']);
$field->addValidator('numeric'); // explicit a validator

// use keyword new
$field = new Field([new Filter\Trim(), new Validator\NotEmpty(), new Validator\Numeric()]);

// use magic method
$field = new Field([Filter::trim(), Validator::notEmpty(), Validator::numeric()]);
```  

### Filter And Validate

As there is no magic you can use filters and validators directly or directly use a field for combined filters and
validators for a specific value. And last but not least you can pass multiple values to the gate to validate and filter
all of them at once.

```php?start_inline=true
use Verja\Gate;
use Verja\Field;
use Verja\Validator;
use Verja\Filter;

$validator = new Validator\Contains('foo');
var_dump($validator->validate('some foo may happen')); // true

$filter = new Filter\Trim('/');
var_dump($filter->filter('/relative/path')); // "relative/path"

$field = new Field(['trim:/ ', 'notEmpty']);
var_dump($filtered = $field->filter(' /relative/path /')); // "relative/path"
var_dump($field->validate($filtered)); // true

$gate = new Gate(['foo' => 'from constructor']);
$gate->accepts([
    'foo' => [ 'contains:bar' ],
    'pw' => [ 'required', 'strLen:3', 'equals:pw_conf' ],
]);
var_dump($gate->validate()); // false (no pw given, foo does not contain bar)
var_dump($gate->validate(['foo' => 'bar', 'pw' => 'abc', 'pw_conf' => 'abc'])); // true
$gate->setData(['foo' => 'bar', 'pw' => '123', 'pw_conf' => 'abc']);
$gate->getData(); // throws "Invalid pw: value should be equal to contexts pw_conf"
```

#### Assert Valid Values

You can also directly assert that a value is valid against a list of filters and validators with the static method
`Gate::assert()`. This is also used in filters that require specific value. It throws an `InvalidValue` exception if
the value is not valid. Example:

```php?start_inline=true
use Verja\Gate;
$id = Gate::assert('integer', $_GET['id']);
```

### Show Errors

The `Validator` may contain an `Verja\Error` after validating an invalid value that you can retrieve with
`Validator::getError()`.

The `Field` contains an array of all errors occurred during `Field::validate()` and the `Gate` contains an array with
all arrays of errors from the fields. The method `Gate::getErrors()` may return something like this:

```php?start_inline=true
return [
    'foo' => [
        new \Verja\Error(
            'NOT_CONTAINS',
            'any string',
            'value should contain "bar"',
            [ 'subString' => 'bar' ]
        )
    ],
    'pw' => [
        new \Verja\Error(
            'STRLEN_TOO_SHORT',
            'abc123',
            'value should be at least 8 characters long',
            [ 'min' => 8, 'max' => 0 ]
        ),
        new \Verja\Error(
            'NOT_EQUAL',
            'abc123',
            'value should be equal to contexts pw_conf',
            [ 'opposite' => 'pw_conf', 'jsonEncode' => true ]
        )
    ],
];
```

You can then serialize this data to this json:

```json
{
   "foo": [
       {
           "key": "NOT_CONTAINS",
           "message": "value should contain \"bar\"",
           "parameters": {
               "subString": "bar",
               "value": "any string"
           }
       }
   ],
   "pw": [
       {
           "key": "STRLEN_TOO_SHORT",
           "message": "value should be at least 8 characters long",
           "parameters": {
               "min": 8,
               "max": 0,
               "value": "abc123"
           }
       },
       {
           "key": "NOT_EQUAL",
           "message": "value should be equal to contexts pw_conf",
           "parameters": {
               "opposite": "pw_conf",
               "jsonEncode": true,
               "value": "abc123"
           }
       }
   ]
}
``` 

### Example

This is a basic real world example:

```php?start_inline=true
use Verja\Gate;
use Verja\Validator;
use Verja\Error;

// imagine a class that persists somewhere somehow (maybe ORM\Entity from tflori\orm?)
use App\Model\User;

// imagine a dependency injection system (maybe tflori/dependency-injector?)
use DependencyInjector\DI;

$gate = new Gate();
$gate->accepts([
    'username' => [ 'required', 'trim', 'strLen:3:20', new Validator\Callback(function ($value) {
        return DI::get('em')->fetch(User::class)->where('username', $value)->count() ?
            new Error('USERNAME_TAKEN', $value, 'value should be unique in user.username') : 
            true;
    })],
    'password' => [ 'required', 'strLen:8', 'equals:password_confirmation' ],
    'email'    => [ 'required', 'trim', 'emailAddress' ],
    'age'      => [ 'integer' ],
]);

if ($_SERVER['REQUEST_METHOD'] === 'post' && $gate->validate($_POST)) {
    $user = new User;
    $user->username = $gate->get('username');
    $user->password = password_hash($gate->get('password'), PASSWORD_BCRYPT);
    $user->email    = $gate->get('email');
    $user->age      = $gate->get('age');
    $user->save();
}
```
