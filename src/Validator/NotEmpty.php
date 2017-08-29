<?php

namespace Verja\Validator;

use Verja\Validator;

class NotEmpty extends Validator
{
    /**
     * Validate $value
     *
     * @return bool
     */
    public function validate($value)
    {
        return !empty($value);
    }
}
