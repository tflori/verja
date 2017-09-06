<?php

namespace Verja\Test\Examples\CustomValidator;

use Verja\Validator;

class Unknown extends Validator
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
        return true;
    }
}
