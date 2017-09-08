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

    /**
     * Get the error for the last validation
     *
     * Returns an array with:
     *
     *  - `key` The message key for translation
     *  - `message` A simple error message in english (optional)
     *  - `value` The value that got validated
     *  - `parameters` An array of parameters for validation (optional)
     *
     * @return array
     */
    public function getError();

    /**
     * Get the inverse error for $value
     *
     * @param mixed $value
     * @return array
     */
    public function getInverseError($value);
}
