<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class AlphaNumeric extends Validator
{
    /** @var bool */
    protected $allowSpaces;

    /**
     * Alpha constructor.
     * @param bool $allowSpaces
     */
    public function __construct(bool $allowSpaces = false)
    {
        $this->allowSpaces = $allowSpaces;
    }

    /**
     * Validate $value
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        $regex = '/^[\pL\pM\pN' . ($this->allowSpaces ? ' ' : '') . ']*$/u';
        if (preg_match($regex, $value)) {
            return true;
        }

        $this->error = new Error(
            'CONTAINS_NON_ALPHANUMERIC',
            $value,
            'value should not contain non alphanumeric characters'
        );
        return false;
    }
}
