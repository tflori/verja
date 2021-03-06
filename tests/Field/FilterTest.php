<?php

namespace Verja\Test\Field;

use Verja\Error;
use Verja\Exception\InvalidValue;
use Verja\Field;
use Verja\Filter\Trim;
use Verja\Gate;
use Verja\Test\Examples\NotSerializable;
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

    /** @test */
    public function acceptsFunctionForFilters()
    {
        $field = new Field();

        $field->addFilter(function () {
            return 'filtered';
        });

        self::assertSame('filtered', $field->filter('value'));
    }

    /** @test */
    public function throwsWhenNoFilterGiven()
    {
        $field = new Field();

        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('$filter has to be an instance of FilterInterface');

        $field->addFilter(new Gate()); // something that is not a filter
    }

    /** @test */
    public function assignsTheField()
    {
        $field = new Field();
        $filter = \Mockery::mock(Trim::class)->makePartial();
        $filter->shouldReceive('assign')->with($field)->once()->andReturnSelf();

        $field->addFilter($filter);
    }

    /** @test */
    public function storesFilteredValue()
    {
        $field = new Field();
        $filter = \Mockery::mock(Trim::class)->makePartial();
        $field->addFilter($filter);
        $filter->shouldReceive('filter')->with(' body ', [])->once()->andReturn('body');

        $field->filter(' body ');
        $field->filter(' body ');
    }

    /** @test */
    public function basedOnValueHash()
    {
        $field = new Field();
        $filter = \Mockery::mock(Trim::class)->makePartial();
        $field->addFilter($filter);
        $filter->shouldReceive('filter')->with(' body ', [])->once()->andReturn('body');
        $filter->shouldReceive('filter')->with('body', [])->once()->andReturn('body');

        $field->filter(' body ');
        $field->filter('body');
    }

    /** @test */
    public function catchesSerializeExceptions()
    {
        $unserializeableValue = new NotSerializable();
        $field = new Field();
        $filter = \Mockery::mock(Trim::class)->makePartial();
        $field->addFilter($filter);
        $filter->shouldReceive('filter')->with($unserializeableValue, [])->twice()->andReturn('body');

        $field->filter($unserializeableValue);
        $field->filter($unserializeableValue);
    }

    /** @test */
    public function resetsCacheWhenFiltersChange()
    {
        $field = new Field();
        $filter = \Mockery::mock(Trim::class)->makePartial();
        $field->addFilter($filter);
        $filter->shouldReceive('filter')->with(' body ', [])->twice()->andReturn('body');

        $field->filter(' body ');
        $field->addFilter('trim');
        $field->filter(' body ');
    }

    /** @test */
    public function catchesInvalidValueException()
    {
        $field = new Field();
        $filter = \Mockery::mock(Trim::class)->makePartial();
        $field->addFilter($filter);
        $filter->shouldReceive('filter')->with(' body ', [])->once()
            ->andThrow(new InvalidValue('does not matter'));

        $result = $field->filter(' body ');

        self::assertSame(' body ', $result);
    }

    /** @test */
    public function invalidatesWithoutValidators()
    {
        $field = new Field();
        $filter = \Mockery::mock(Trim::class)->makePartial();
        $field->addFilter($filter);
        $filter->shouldReceive('filter')->with(' body ', [])->once()
            ->andThrow(new InvalidValue('does not matter'));
        $field->filter(' body ');

        self::assertFalse($field->validate(' body '));
    }

    /** @test */
    public function storesErrorsFromException()
    {
        $error = new Error('A_ERROR', ' body ');
        $field = new Field();
        $filter = \Mockery::mock(Trim::class)->makePartial();
        $field->addFilter($filter);
        $filter->shouldReceive('filter')->with(' body ', [])->once()
            ->andThrow(new InvalidValue('does not matter', $error));

        $field->filter(' body ');

        self::assertSame([$error], $field->getErrors());
    }
}
