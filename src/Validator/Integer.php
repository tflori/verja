<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Integer extends Validator
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
        if (!is_int($value) && (!is_numeric($value) || (int)$value != (double)$value)) {
            $this->error = new Error('NO_INTEGER', $value, 'value should be an integer');
            return false;
        }

        return true;
    }

    public function getInverseError($value)
    {
        return new Error('IS_INTEGER', $value, 'value should not be an integer');
    }
}
