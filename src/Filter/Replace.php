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
     * @return mixed
     */
    public function filter($value)
    {
        return str_replace($this->search, $this->replace, $value);
    }
}
