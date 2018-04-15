<?php

namespace Verja\Test\Validator;

use Verja\Test\TestCase;
use Verja\Validator\NotEmpty;

class CommonTest extends TestCase
{
    /** @test */
    public function validatorIsInvokable()
    {
        $validator = new NotEmpty();

        $result = $validator('foo');

        self::assertTrue($result);
    }
}
