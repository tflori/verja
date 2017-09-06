<?php

namespace Verja\Filter;

use Verja\Filter;

class Replace extends Filter
{
    /** @var array|string */
    protected $replace;

    /** @var array|string */
    protected $search;

    /**
     * Replace constructor.
     *
     * @param array|string $search
     * @param array|string $replace
     */
    public function __construct($search, $replace)
    {
        $this->replace = $replace;
        $this->search = $search;
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
        return str_replace($this->search, $this->replace, $value);
    }
}
