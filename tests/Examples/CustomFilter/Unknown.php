<?php

namespace Verja\Test\Examples\CustomFilter;

use Verja\Filter;

class Unknown extends Filter
{
    /**
     * Filter $value
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter($value, array $context = [])
    {
        return $value;
    }
}
