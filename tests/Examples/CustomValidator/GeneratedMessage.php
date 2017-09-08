<?php

namespace Verja\Test\Examples\CustomValidator;

use Verja\Validator;

class GeneratedMessage extends Validator
{
    public function validate($value, array $context = []): bool
    {
        $this->error = $this->buildError('GENERATED_MESSAGE', $value);
        return false;
    }
}
