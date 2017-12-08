<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Truthful extends Validator
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
        if (!$value) {
            $this->error = new Error('NOT_TRUTHFUL', $value, 'value should be truthful');
            return false;
        }

        return true;
    }

    public function getInverseError($value)
    {
        return new Error('IS_TRUTHFUL', $value, 'value should not be truthful');
    }
}
