<?php

namespace Verja\Filter;

use Verja\Filter;

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
        $this->validate(new \Verja\Validator\Numeric($this->decimalPoint), $value);

        if ($this->decimalPoint !== '.' && is_string($value)) {
            $value = str_replace($this->decimalPoint, '.', $value);
        }

        return !is_numeric($value) ? $value : ((int)$value == (double)$value ? (int)$value : (double)$value);
    }
}
