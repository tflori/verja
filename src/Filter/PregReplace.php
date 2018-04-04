<?php

namespace Verja\Filter;

use Verja\Filter;

class PregReplace extends Filter
{
    /** @var string */
    protected $pattern;

    /** @var string */
    protected $replace;

    /**
     * PregReplace constructor.
     *
     * @param string|array    $pattern
     * @param string|callable $replace
     */
    public function __construct($pattern, $replace)
    {
        $this->pattern = $pattern;
        $this->replace = $replace;
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
        if (!is_string($value)) {
            return $value;
        }

        if (is_callable($this->replace)) {
            return preg_replace_callback($this->pattern, $this->replace, $value);
        }

        return preg_replace($this->pattern, $this->replace, $value);
    }
}
