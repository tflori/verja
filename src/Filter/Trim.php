<?php

namespace Verja\Filter;

use Verja\Filter;

class Trim extends Filter
{
    protected $characterMask;

    /**
     * Trim constructor.
     *
     * @param string $characterMask
     * @see trim()
     */
    public function __construct(string $characterMask = " \t\n\r\0\x0B")
    {
        $this->characterMask = $characterMask;
    }

    /**
     * Trim $value
     *
     * If $value is not a string it is not touched.
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function filter($value, array $context = [])
    {
        return is_string($value) ? trim($value, $this->characterMask) : $value;
    }
}
