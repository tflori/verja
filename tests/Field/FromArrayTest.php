<?php

namespace Verja\Test\Field;

use Verja\Exception\NotFound;
use Verja\Field;
use Verja\Test\TestCase;
use Verja\Validator\Contains;
use Verja\Validator\Not;

class FromArrayTest extends TestCase
{
    /** @test */
    public function addsFiltersFromDefinitions()
    {
        $field = new Field(['trim']);

        self::assertEquals((new Field)->addFilter('trim'), $field);
    }

    /** @test */
    public function addsValidatorsFromDefinitions()
    {
        $field = new Field(['notEmpty']);

        self::assertEquals((new Field)->addValidator('notEmpty'), $field);
    }

    /** @test */
    public function acceptsMixedValidatorsAndFilters()
    {
        $field = new Field(['notEmpty', 'trim', '!contains: ']);

        self::assertEquals(
            (new Field)
                ->addFilter('trim')
                ->addValidator('notEmpty')
                ->addValidator(new Not(new Contains(' '))),
            $field
        );
    }

    /** @test */
    public function throwsNotFound()
    {
        self::expectException(NotFound::class);
        self::expectExceptionMessage('No filter or validator named \'Unknown\' found');
        new Field(['unknown:42']);
    }
}
