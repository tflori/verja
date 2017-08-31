<?php

namespace Verja\Test\Field;

use PHPUnit\Framework\TestCase;
use Verja\Field;
use Verja\Filter\Trim;

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
}
