<?php

namespace Verja\Test\Filter;

use Verja\Test\Examples\CustomFilter\Validated;
use Verja\Test\TestCase;
use Verja\Validator\Integer;

class ValidatedByTest extends TestCase
{
    /** @test */
    public function returnsAnValidator()
    {
        $filter = new Validated('integer');

        $result = $filter->getValidatedBy();

        self::assertInstanceOf(Integer::class, $result);
    }
}
