<?php

namespace Verja\Test\Validator;

use Verja\Error;
use Verja\Test\TestCase;
use Verja\Validator\IpAddress;

class IpAddressTest extends TestCase
{
    /** @dataProvider provideValidIpAddresses
     * @param $version
     * @param $ipAddress
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
            [IpAddress::VERSION_ANY, '0.0.0.0'],
            [IpAddress::VERSION_ANY, '::'],
            [IpAddress::VERSION_4, '127.0.0.1'],
            [IpAddress::VERSION_6, '::1'],
        ];
    }

    /** @dataProvider provideInvalidIpAddresses
     * @param $version
     * @param $ipAddress
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
            [IpAddress::VERSION_4, '::'],
            [IpAddress::VERSION_6, '0.0.0.0'],
            [IpAddress::VERSION_ANY, '0.0::1']
        ];
    }

    /** @dataProvider provideInvalidRange
     * @param $range
     * @param $ipAddress
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
            [IpAddress::RANGE_PRIVATE, '87.23.12.41'],
            [IpAddress::RANGE_PUBLIC, '192.168.0.1'],
            [IpAddress::RANGE_PUBLIC, '10.10.10.1'],
            [IpAddress::RANGE_PUBLIC, 'fd00::1'],
            [IpAddress::RANGE_PUBLIC . ',' . IpAddress::RANGE_UNRESERVED, '172.31.255.255'],
            [IpAddress::RANGE_PUBLIC . ',' . IpAddress::RANGE_UNRESERVED, '240.0.0.1'],
            [IpAddress::RANGE_PUBLIC . ',' . IpAddress::RANGE_UNRESERVED, '127.0.0.1'],
            [IpAddress::RANGE_PUBLIC . ',' . IpAddress::RANGE_UNRESERVED, '::1'],
        ];
    }

    /** @dataProvider provideErroneousIpAddresses
     * @param $version
     * @param $range
     * @param $ip
     * @param $eKey
     * @param $eMessage
     * @test */
    public function storesErrors($version, $range, $ip, $eKey, $eMessage)
    {
        $validator = new IpAddress($version, $range);

        $validator->validate($ip);

        self::assertEquals(new Error($eKey, $ip, $eMessage, [
            'version' => $version,
            'range' => $range,
        ]), $validator->getError());
    }

    public function provideErroneousIpAddresses()
    {
        return [
            ['any', 'any', 'anything', 'NO_IP_ADDRESS', 'value should be an ip address'],
            ['v4', 'any', '::1', 'NO_IP_ADDRESS', 'value should be an ip-v4 address'],
            ['v6', 'any', '127.0.0.1', 'NO_IP_ADDRESS', 'value should be an ip-v6 address'],
            ['any', 'private', '87.23.12.41', 'IS_PUBLIC_IP_ADDRESS', 'value should not be a public ip address'],
            ['any', 'public', '192.168.0.1', 'NO_PUBLIC_IP_ADDRESS', 'value should be a public ip address'],
            [
                'any',
                'public,unreserved',
                '240.0.0.1',
                'IS_RESERVED_IP_ADDRESS',
                'value should not be a reserved ip address'
            ],
        ];
    }
}
