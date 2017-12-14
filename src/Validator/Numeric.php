<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Numeric extends Validator
{
    /** @var string */
    protected $decimalPoint;

    /**
     * Numeric constructor.
     *
     * @param string $decimalPoint
     */
    public function __construct(string $decimalPoint = '.')
    {
        $this->decimalPoint = $decimalPoint;
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
        if ($this->decimalPoint !== '.' && is_string($value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace($this->decimalPoint, '.', $value);
        }

        if (!is_int($value) && !is_double($value) && !is_numeric($value)) {
            $this->error = new Error('NOT_NUMERIC', $value, 'value should be numeric');
            return false;
        }

        return true;
    }

    public function getInverseError($value)
    {
        return new Error('IS_NUMERIC', $value, 'value should not be numeric');
    }
}
