<?php

namespace Verja\Validator;

use Verja\Validator;

class NotEmpty extends Validator
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
        return !empty($value);
    }
}
