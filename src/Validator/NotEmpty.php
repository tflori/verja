<?php

namespace Verja\Validator;

use Verja\Validator;

class NotEmpty extends Validator
{
    /**
     * Validate $value
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool
    {
        return !empty($value);
    }
}
