<?php

namespace Verja;

interface FilterInterface
{
    /**
     * Filter $value
     *
     * @param mixed $value
     * @return mixed
     */
    public function filter($value);
}
