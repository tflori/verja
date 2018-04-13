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
     * Call the filter is an alias for filter
     *
     * @param mixed $value
     * @param array $context
     * @return mixed
     */
    public function __invoke($value, array $context = []);

    /**
     * Assign filter to $field
     *
     * @param Field $field
     * @return $this
     */
    public function assign(Field $field);
}
