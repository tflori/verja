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
     * Call the validator is an alias for validate
     *
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public function __invoke($value, array $context = []): bool;

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
     * Returns an Error object
     *
     * @return Error
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
