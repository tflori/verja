<?php

namespace Verja\Test\Field;

use Verja\Field;
use Verja\Filter\Trim;
use Verja\Test\TestCase;

class AddFiltersFromArrayTest extends TestCase
{
    /** @test */
    public function returnsArray()
    {
        $field = new Field;

        $result = $field->addFiltersFromArray([]);

        self::assertSame([], $result);
    }

    /** @test */
    public function addsFilters()
    {
        $field = new Field;

        $field->addFiltersFromArray(['trim', 'replace:a:b']);

        self::assertEquals(
            (new Field)
                ->addFilter('trim')
                ->addFilter('replace:a:b'),
            $field
        );
    }

    /** @test */
    public function catchesFilterNotFound()
    {
        $field = new Field;

        $field->addFiltersFromArray(['unknownFilter']);

        self::assertEquals(new Field, $field);
    }

    /** @test */
    public function returnsNotFoundFilter()
    {
        $field = new Field;

        $result = $field->addFiltersFromArray(['trim', 'unknownFilter:42']);

        self::assertSame(['UnknownFilter'], $result);
        self::assertEquals((new Field)->addFilter('trim'), $field);
    }

    /** @test */
    public function acceptsFilters()
    {
        $field = new Field;

        $field->addFiltersFromArray([new Trim]);

        self::assertEquals((new Field)->addFilter('trim'), $field);
    }
}
