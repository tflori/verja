<?php

namespace Verja\Filter;

use Verja\Filter;

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
        $this->validate(new \Verja\Validator\Integer(), $value);
        return (!is_numeric($value) || (int)$value != (double)$value) ? $value : (int)$value;
    }
}
