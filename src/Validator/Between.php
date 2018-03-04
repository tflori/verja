<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Between extends Validator
{
    /** @var float|int */
    protected $min;

    /** @var float|int */
    protected $max;

    /**
     * Between constructor.
     *
     * @param float|int $min
     * @param float|int $max
     */
    public function __construct($min = null, $max = null)
    {
        $this->min = $min;
        $this->max = $max;
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
        if (!is_int($value) && !is_double($value)) {
            $this->error = new Error('NO_NUMBER', $value, 'value should be a number');
            return false;
        }

        if (!is_null($this->min) && $this->min > $value) {
            $this->error = new Error(
                'TOO_SMALL',
                $value,
                sprintf('value should be at least %s', $this->min),
                [
                    'min' => $this->min,
                    'max' => $this->max,
                ]
            );
            return false;
        }

        if (!is_null($this->max) && $this->max < $value) {
            $this->error = new Error(
                'TOO_BIG',
                $value,
                sprintf('value should be maximal %s', $this->max),
                [
                    'min' => $this->min,
                    'max' => $this->max,
                ]
            );
            return false;
        }

        return true;
    }
}
