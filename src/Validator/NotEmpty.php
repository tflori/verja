<?php

namespace Verja\Validator;

use Verja\Validator;

class NotEmpty extends Validator
{
    /** {@inheritdoc} */
    public function validate($value, array $context = []): bool
    {
        if (empty($value)) {
            $this->error = $this->buildError(
                'IS_EMPTY',
                $value,
                null,
                'value should not be empty'
            );
            return false;
        }

        return true;
    }

    /** {@inheritdoc} */
    public function getInverseError($value)
    {
        return $this->buildError(
            'IS_NOT_EMPTY',
            $value,
            null,
            'value should be empty'
        );
    }
}
