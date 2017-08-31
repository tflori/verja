<?php

namespace Verja\Validator;

use Verja\Validator;

class StrLen extends Validator
{
    /** @var int */
    protected $min;

    /** @var int */
    protected $max;

    /**
     * StrLen constructor.
     *
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max = 0)
    {
        $this->min = $min;
        $this->max = $max;
    }


    /**
     * Validate $value
     *
     * @return bool
     */
    public function validate($value): bool
    {
        $strlen = strlen($value);
        return $strlen >= $this->min && ($this->max === 0 || $strlen <= $this->max);
    }
}
