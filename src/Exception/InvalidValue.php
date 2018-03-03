<?php

namespace Verja\Exception;

use Verja\Error;
use Verja\Exception;

class InvalidValue extends Exception
{
    /** @var Error[] */
    public $errors = [];

    public function __construct($message = "", Error ...$errors)
    {
        parent::__construct($message);

        $this->errors = $errors;
    }
}
