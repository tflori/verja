<?php

namespace Verja\Test\Field;

use Verja\Field;
use Verja\Filter\Trim;
use Verja\Test\TestCase;

class FilterTest extends TestCase
{
    /** @test */
    public function executesAllFilters()
    {
        $field = new Field();
        $field->addFilter(new Trim(' '));
        $field->addFilter(new Trim('/'));

        $result = $field->filter(' body/');

        self::assertSame('body', $result);
    }

    /** @test */
    public function executesInSpecificOrder()
    {
        $field = new Field();
        $field->appendFilter(new Trim(' '));
        $field->prependFilter(new Trim('/'));

        $result = $field->filter('/ body');

        self::assertSame('body', $result);
    }

    /** @test */
    public function allowsStringForFilters()
    {
        $field = new Field();

        $field->appendFilter('trim:/');

        self::assertSame('body', $field->filter('/body/'));
    }

//    /** @test */
//    public function storesFilteredValue()
//    {
//        $field = new Field();
//        $filter = \Mockery::mock(Trim::class)->makePartial();
//        $field->addFilter($filter);
//        $filter->shouldReceive('filter')->with(' body ')->once()->andReturn('body');
//
//        $field->filter(' body ');
//        $result = $field->filter(' body ');
//
//        self::assertSame('body', $result);
//    }
}
