<?php

namespace Verja\Filter;

use Verja\Filter;
use Verja\Validator;

class Numeric extends Filter
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
     * Filter $value
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function filter($value, array $context = [])
    {
        Validator::assert(new Validator\Numeric($this->decimalPoint), $value);

        if ($this->decimalPoint !== '.' && is_string($value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace($this->decimalPoint, '.', $value);
        }

        return (double)$value === round((double)$value) ? (int)((double)$value) : (double)$value;
    }
}
