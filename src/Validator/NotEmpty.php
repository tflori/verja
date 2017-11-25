<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class NotEmpty extends Validator
{
    /** {@inheritdoc} */
    public function validate($value, array $context = []): bool
    {
        if (empty($value)) {
            $this->error = new Error(
                'IS_EMPTY',
                $value,
                'value should not be empty'
            );
            return false;
        }

        return true;
    }

    /** {@inheritdoc} */
    public function getInverseError($value)
    {
        return new Error(
            'IS_NOT_EMPTY',
            $value,
            'value should be empty'
        );
    }
}
