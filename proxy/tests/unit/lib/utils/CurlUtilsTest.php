<?php

namespace Weave\Proxy\Tests;

use PHPUnit\Framework\TestCase;
use Weave\Proxy\CurlUtils;

require_once __DIR__ . '/../../../../source/lib/utils/CurlUtils.php';

class CurlUtilsTest extends TestCase
{
    public function testBuildHeaderLinesWithSingleHeader()
    {
        $headers = ['Content-Type' => 'application/json'];
        $result = CurlUtils::buildHeaderLines($headers);

        $this->assertEquals(['Content-Type: application/json'], $result);
    }

    public function testBuildHeaderLinesWithMultipleHeaders()
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer token123',
            'Accept' => 'application/json'
        ];

        $result = CurlUtils::buildHeaderLines($headers);

        $expected = [
            'Content-Type: application/json',
            'Authorization: Bearer token123',
            'Accept: application/json'
        ];

        $this->assertEquals($expected, $result);
    }

    public function testBuildHeaderLinesWithEmptyHeaders()
    {
        $headers = [];
        $result = CurlUtils::buildHeaderLines($headers);

        $this->assertEquals([], $result);
    }

    public function testBuildHeaderLinesWithValueContainingColon()
    {
        $headers = ['X-Custom' => 'value:with:colons'];
        $result = CurlUtils::buildHeaderLines($headers);

        $this->assertEquals(['X-Custom: value:with:colons'], $result);
    }

    public function testBuildHeaderLinesWithEmptyValue()
    {
        $headers = ['X-Empty' => ''];
        $result = CurlUtils::buildHeaderLines($headers);

        $this->assertEquals(['X-Empty: '], $result);
    }

    public function testBuildHeaderLinesWithNumericValue()
    {
        $headers = ['Content-Length' => '1234'];
        $result = CurlUtils::buildHeaderLines($headers);

        $this->assertEquals(['Content-Length: 1234'], $result);
    }
}
