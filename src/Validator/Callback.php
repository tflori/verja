<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class Callback extends Validator
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

    /** {@inheritdoc} */
    public function validate($value, array $context = []): bool
    {
        $result = call_user_func($this->callback, $value, $context);

        if ($result instanceof Error) {
            $this->error = $result;
            return false;
        }

        return (bool)$result;
    }
}
