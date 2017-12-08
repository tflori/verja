<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Numeric extends Validator
{
    /**
     * Validate $value
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        if (!is_int($value) && !is_double($value)) {
            $this->error = new Error('NOT_NUMERIC', $value, 'value should be numeric');
            return false;
        }

        return true;
    }

    public function getInverseError($value)
    {
        return new Error('IS_NUMERIC', $value, 'value should not be numeric');
    }
}
