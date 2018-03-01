<?php

namespace Verja\Validator;

use Verja\Error;
use Verja\Validator;

class InArray extends Validator
{
    /** @var array|string|\Traversable */
    protected $array;

    /**
     * InArray constructor.
     *
     * @param array|string $array
     */
    public function __construct($array)
    {
        if (!is_array($array) && !is_string($array) && !$array instanceof \Traversable) {
            throw new \InvalidArgumentException('$array has to be from type string, array or Traversable');
        }

        $this->array = $array;
    }


    /**
     * Validate $value
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool
    {
        if (!$this->inArray($value)) {
            $this->error = new Error('NOT_IN_ARRAY', $value, 'value should be in array', ['array' => $this->array]);
            return false;
        }

        return true;
    }

    protected function inArray($value)
    {
        if (is_array($this->array)) {
            return in_array($value, $this->array);
        }

        if (is_string($this->array)) {
            $this->array = explode(',', $this->array);
            return in_array($value, $this->array);
        }

        foreach ($this->array as $v) {
            if ($value == $v) {
                return true;
            }
        }

        return false;
    }
}
