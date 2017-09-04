<?php

namespace Verja\Test\Examples\CustomValidator;

use Verja\Validator;

class Unknown extends Validator
{
    /**
     * Validate $value
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool
    {
        return true;
    }
}
