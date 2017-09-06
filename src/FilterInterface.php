<?php

namespace Verja;

interface FilterInterface
{
    /**
     * Filter $value
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function filter($value, array $context = []);

    /**
     * Assign filter to $field
     *
     * @param Field $field
     * @return $this
     */
    public function assign(Field $field);
}
