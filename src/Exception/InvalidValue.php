<?php

namespace Verja\Exception;

use Throwable;
use Verja\Error;
use Verja\Exception;

class InvalidValue extends Exception
{
    /** @var Error[] */
    public $errors = [];

    public function __construct($message = "", $code = 0, Throwable $previous = null, Error ...$errors)
    {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;
    }
}
