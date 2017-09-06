<?php

namespace Verja\Test\Field;

use Verja\Field;
use Verja\Test\Examples\CustomValidator\NotEmpty;
use Verja\Test\TestCase;

class WithAssignedFieldTest extends TestCase
{
    /** @test */
    public function returnsSelf()
    {
        $object = new NotEmpty();
        $field = new Field();

        $result = $object->assign($field);

        self::assertSame($object, $result);
    }

    /** @test */
    public function returnsAClone()
    {
        $object = new NotEmpty();
        $field1 = new Field();
        $object->assign($field1);
        $field2 = new Field();

        $result = $object->assign($field2);

        self::assertNotSame($object, $result);
        self::assertEquals($object, $result);
    }

    /** @test */
    public function doesNotCloneWhenAlreadyAssigned()
    {
        $object = new NotEmpty();
        $field = new Field();
        $object->assign($field);

        $result = $object->assign($field);

        self::assertSame($object, $result);
    }
}
