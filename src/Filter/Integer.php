<?php

namespace Verja\Filter;

use Verja\Filter;
use Verja\Validator;

class Integer extends Filter
{
    /**
     * Filter $value
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function filter($value, array $context = [])
    {
        Validator::assert(new Validator\Integer(), $value);
        return (!is_numeric($value) || (int)$value != (double)$value) ? $value : (int)$value;
    }
}
