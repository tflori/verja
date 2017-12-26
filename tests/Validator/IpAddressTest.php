<?php

namespace Verja\Test\Validator;

use Verja\Test\TestCase;
use Verja\Validator\IpAddress;

class IpAddressTest extends TestCase
{
    /** @dataProvider provideValidIpAddresses
     * @test */
    public function acceptsSpecificTypeofIpAddress($version, $ipAddress)
    {
        $validator = new IpAddress($version);

        $result = $validator->validate($ipAddress);

        self::assertTrue($result);
    }

    public function provideValidIpAddresses()
    {
        return [
            ['any', '0.0.0.0'],
            ['any', '::'],
            ['v4', '127.0.0.1'],
            ['v6', '::1'],
        ];
    }

    /** @dataProvider provideInvalidIpAddresses
     * @test */
    public function acceptsOnlySpecificIpAddress($version, $ipAddress)
    {
        $validator = new IpAddress($version);

        $result = $validator->validate($ipAddress);

        self::assertFalse($result);
    }

    public function provideInvalidIpAddresses()
    {
        return [
            ['v4', '::'],
            ['v6', '0.0.0.0'],
            ['any', '0.0::1']
        ];
    }

    /** @dataProvider provideInvalidRange
     * @test */
    public function acceptsOnlySpecificRange($range, $ipAddress)
    {
        $validator = new IpAddress('any', $range);

        $result = $validator->validate($ipAddress);

        self::assertFalse($result);
    }

    public function provideInvalidRange()
    {
        return [
            ['private', '87.23.12.41'],
            ['public', '192.168.0.1'],
            ['public', '10.10.10.1'],
            ['public', 'fd00::1'],
            ['public,unreserved', '172.31.255.255'],
            ['public,unreserved', '240.0.0.1'],
            ['public,unreserved', '127.0.0.1'],
            ['public,unreserved', '::1'],
        ];
    }
}
