<?php

namespace Verja;

interface ValidatorInterface
{
    /**
     * Validate $value
     *
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool;
}
