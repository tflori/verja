<?php

namespace Verja;

interface ValidatorInterface
{
    /**
     * Validate $value
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function validate($value, array $context = []): bool;

    /**
     * Assign validator to $field
     *
     * @param Field $field
     * @return $this
     */
    public function assign(Field $field);
}
