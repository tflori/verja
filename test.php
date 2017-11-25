<?php

use Verja\Field;
use Verja\Gate;
use DependencyInjector\DI;
use Verja\Validator as vv;

require_once 'vendor/autoload.php';

// we using tflori/dependency-injector here...
DI::set(
    'verja',                     // namne
    function () {                // factory
        return new Gate();
    },
    false                        // share / singleton
);

// this is the controller
function controller()
{
    /** @var Gate $verja */
    $verja = DI::get('verja');
    $field1 = new DField();
    $field2 = new DField();
    $validator = new vv\NotEmpty();

    $field1->addValidator($validator);
    $field2->addValidator($validator);

    var_dump($field1->getValidators());
    var_dump($field2->getValidators());
}

class DField extends Field
{
    public function getValidators()
    {
        return $this->validators;
    }
}

controller();
