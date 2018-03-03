<?php

namespace Verja\Test\Field;

use Verja\Field;
use Verja\Test\TestCase;
use Verja\Validator\NotEmpty;

class AddValidatorsFromArrayTest extends TestCase
{
    /** @test */
    public function returnsArray()
    {
        $field = new Field;

        $result = $field->addValidatorsFromArray([]);

        self::assertSame([], $result);
    }

    /** @test */
    public function addsValidators()
    {
        $field = new Field;

        $field->addValidatorsFromArray(['notEmpty', 'strLen:2:5']);

        self::assertEquals(
            (new Field)
                ->addValidator('notEmpty')
                ->addValidator('strLen:2:5'),
            $field
        );
    }

    /** @test */
    public function catchesValidatorNotFound()
    {
        $field = new Field;

        $field->addValidatorsFromArray(['unknownValidator']);

        self::assertEquals(new Field, $field);
    }

    /** @test */
    public function returnsNotFoundValidator()
    {
        $field = new Field;

        $result = $field->addValidatorsFromArray(['notEmpty', '!unknownValidator:42']);

        self::assertSame(['UnknownValidator' => '!unknownValidator:42'], $result);
        self::assertEquals((new Field)->addValidator('notEmpty'), $field);
    }

    /** @test */
    public function acceptsValidators()
    {
        $field = new Field;

        $field->addValidatorsFromArray([new NotEmpty]);

        self::assertEquals((new Field)->addValidator('notEmpty'), $field);
    }
}
