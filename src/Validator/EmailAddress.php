<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class EmailAddress extends Validator
{
    const LOCAL_PART_PATTERN = '[A-Za-z0-9.!#$%&\'*+-\/=?^_`{|}~.]+';
    const DOMAIN_PART_PATTERN = '(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])';

    /** {@inheritdoc} */
    public function validate($value, array $context = []): bool
    {
        if (!preg_match('/^' . self::LOCAL_PART_PATTERN . '@' . self::DOMAIN_PART_PATTERN . '$/', $value)) {
            $this->error = new Error(
                'NO_EMAIL_ADDRESS',
                $value,
                'value should be a valid email address',
                null
            );
            return false;
        }

        return true;
    }

    /** {@inheritdoc} */
    public function getInverseError($value)
    {
        return new Error(
            'IS_EMAIL_ADDRESS',
            $value,
            'value should not be an email address',
            null
        );
    }
}
