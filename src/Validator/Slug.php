<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Slug extends Validator
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
        $regex = '/^[0-9a-z-_]+$/u';
        if (preg_match($regex, $value)) {
            return true;
        }

        $this->error = new Error(
            'NO_SLUG',
            $value,
            'value should be a valid slug'
        );
        return false;
    }
}
