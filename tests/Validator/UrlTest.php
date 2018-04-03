<?php

namespace Verja\Test\Validator;

use Mockery as m;
use Mockery\Mock;
use Verja\Error;
use Verja\Test\Examples\Helper;
use Verja\Test\TestCase;
use Verja\Validator\Url;

class UrlTest extends TestCase
{
    /** @var Helper|Mock */
    protected $helperMock;

    protected function setUp()
    {
        parent::setUp();

        $this->helperMock = m::mock(Helper::class)->makePartial();
        Url::setSocketTester([$this->helperMock, 'testSocket']);
        Url::setDnsTester([$this->helperMock, 'testDns']);
    }

    /** @test */
    public function nonStringValuesAreInvalid()
    {
        $validator = new Url();

        $result = $validator->validate(42);

        self::assertFalse($result);
        self::assertEquals(new Error(
            'NO_URL',
            42,
            'value should be a valid url'
        ), $validator->getError());
    }

    /** @dataProvider provideInvalidUrls
     * @param $value
     * @test */
    public function invalidUrlsAreInvalid($value)
    {
        $validator = new Url();

        $result = $validator->validate($value);

        self::assertFalse($result);
        self::assertEquals(new Error(
            'NO_URL',
            $value,
            'value should be a valid url'
        ), $validator->getError());
    }

    /** @test */
    public function expectsCompleteUrlByDefault()
    {
        $validator = new Url();

        $result = $validator->validate('/just/a/path');

        self::assertFalse($result);
        self::assertEquals(new Error(
            'NOT_FULL_URL',
            '/just/a/path',
            'value should be a full url including host and scheme',
            ['schemes' => ['https', 'http', 'ftp']]
        ), $validator->getError());
    }

    /** @test */
    public function fullUrlHasToContainScheme()
    {
        $validator = new Url();

        $result = $validator->validate('//host/path');

        self::assertFalse($result);
        self::assertEquals(new Error(
            'NOT_FULL_URL',
            '//host/path',
            'value should be a full url including host and scheme',
            ['schemes' => ['https', 'http', 'ftp']]
        ), $validator->getError());
    }

    /** @test */
    public function byDefaultOnlyHttpAndFtpAllowed()
    {
        $validator = new Url();

        $result = $validator->validate('scheme://host/path');

        self::assertFalse($result);
        self::assertEquals(new Error(
            'SCHEME_NOT_ALLOWED',
            'scheme://host/path',
            'value should contain an allowed scheme',
            ['schemes' => ['https', 'http', 'ftp']]
        ), $validator->getError());
    }

    /** @dataProvider provideFileUrls
     * @param $value
     * @test */
    public function allowsOtherSchemes($value)
    {
        $validator = new Url('complete', 'ftp', 'smb', 'file');

        self::assertTrue($validator->validate($value));
    }

    /** @test */
    public function allowsRelativeUrls()
    {
        $validator = new Url('valid');

        $result = $validator->validate('where/ever');

        self::assertTrue($result);
    }

    /** @test */
    public function checksForActiveDnsRecord()
    {
        $validator = new Url('active');
        $this->helperMock->shouldReceive('testDns')
            ->with('example.com')
            ->once()
            ->andReturn(true);

        $result = $validator->validate('https://example.com');

        self::assertTrue($result);
    }

    /** @test */
    public function storesErrorWithoutActiveDnsRecord()
    {
        $validator = new Url('active');
        $this->helperMock->shouldReceive('testDns')
            ->with('example.info')
            ->once()
            ->andReturn(false);

        $result = $validator->validate('https://example.info');

        self::assertFalse($result);
        self::assertEquals(new Error(
            'NO_DNS_RECORD',
            'https://example.info',
            'value should contain a hostname with an active dns record'
        ), $validator->getError());
    }

    /** @test */
    public function checksForListeningServer()
    {
        $validator = new Url('listening');
        $this->helperMock->shouldReceive('testSocket')
            ->with('example.com', 443)
            ->once()
            ->andReturn(true);

        $result = $validator->validate('https://example.com');

        self::assertTrue($result);
    }

    /** @test */
    public function storesErrorIfPortIsUnknown()
    {
        $validator = new Url('listening', ['hlsw', 'cs']);
        $this->helperMock->shouldNotReceive('testSocket');

        $result = $validator->validate('hlsw://example.com');

        self::assertFalse($result);
        self::assertEquals(new Error(
            'NO_PORT',
            'hlsw://example.com',
            'value should contain a port'
        ), $validator->getError());
    }

    /** @test */
    public function storesErrorWithoutListeningServer()
    {
        $validator = new Url('listening');
        $this->helperMock->shouldReceive('testSocket')
            ->with('example.net', 443)
            ->once()
            ->andReturn(false);

        $result = $validator->validate('https://example.net');

        self::assertFalse($result);
        self::assertEquals(new Error(
            'NOT_LISTENING',
            'https://example.net',
            'value should point to a server that is currently listening'
        ), $validator->getError());
    }

    public function provideInvalidUrls()
    {
        return [
            [':'],
            ['//'],
            ['//example.com:65536'], // port out of range
            ['//example.com:0'], // port out of range
            ['//:'],
        ];
    }

    public function provideFileUrls()
    {
        return [
            ['ftp://example.com/path/file.ext'],
            ['smb://server/share/path/file.ext'],
            ['file:///path/file.ext'],
            ['file://c/path/file.ext'],
        ];
    }
}
