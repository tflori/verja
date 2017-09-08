<?php

namespace Verja\Filter;

use Verja\Filter;

class Callback extends Filter
{
    /** @var callable */
    protected $callback;

    /**
     * Callback constructor.
     *
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
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
        return call_user_func($this->callback, $value, $context);
    }
}
