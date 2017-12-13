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
        $this->setValidatedBy(new \Verja\Validator\Numeric($decimalPoint));
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
        if ($this->decimalPoint !== '.') {
            $value = str_replace($this->decimalPoint, '.', $value);
        }

        return !is_numeric($value) ? $value : ((int)$value == (double)$value ? (int)$value : (double)$value);
    }
}
