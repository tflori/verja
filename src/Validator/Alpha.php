<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Alpha extends Validator
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
        $regex = '/^[\pL\pM' . ($this->allowSpaces ? ' ' : '') . ']*$/u';
        if (preg_match_all($regex, $value)) {
            return true;
        }

        $this->error = new Error('CONTAINS_NON_ALPHA', $value, 'value should not contain non alphabetical characters');
        return false;
    }
}
