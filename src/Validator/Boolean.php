<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Boolean extends Validator
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
        if (!is_bool($value)) {
            $this->error = new Error('NOT_BOOLEAN', $value, 'value should be a boolean');
            return false;
        }

        return true;
    }

    public function getInverseError($value)
    {
        return new Error('IS_BOOLEAN', $value, 'value should not be a boolean');
    }
}
