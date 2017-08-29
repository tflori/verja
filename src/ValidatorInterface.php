<?php

namespace Verja;

interface ValidatorInterface
{
    /**
     * Validate $value
     *
     * @return bool
     */
    public function validate($value);
}
