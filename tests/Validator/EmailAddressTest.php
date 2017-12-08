<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\EmailAddress;

class EmailAddressTest extends TestCase
{
    /** @dataProvider provideEmailValidations
     * @param $email
     * @param $valid
     * @test */
    public function validatesEmailAddresses($email, $valid)
    {
        $validator = new EmailAddress();

        $result = $validator->validate($email);

        self::assertSame($valid, $result);
    }

    public function provideEmailValidations()
    {
        return [
            ['john.doe@example.com', true],
            ['Aa0.!#$%&\'*+-/=?^_`{|}~.@example.com', true],
            ['john@doe@example.com', false],
            ['johndoe@localhost', false],
            ['@example.com', false],
            ['johndoe@example.c', false],
            ['@', false],
        ];
    }

    /** @test */
    public function setsError()
    {
        $validator = new EmailAddress();

        self::assertFalse($validator->validate('@'));
        self::assertEquals(
            new Error('NO_EMAIL_ADDRESS', '@', 'value should be a valid email address'),
            $validator->getError()
        );
    }

    /** @test */
    public function returnsInverseError()
    {
        $validator = new EmailAddress();

        self::assertEquals(
            new Error('IS_EMAIL_ADDRESS', 'john.doe@example.com', 'value should not be an email address'),
            $validator->getInverseError('john.doe@example.com')
        );
    }
}
