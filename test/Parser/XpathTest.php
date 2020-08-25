<?php

declare(strict_types=1);

namespace XpathReaderTest\Parser;

use PHPUnit\Framework\TestCase;
use XpathReader\Parser\Xpath;

class XpathTest extends TestCase
{
    /**
     * @test
     * @dataProvider providesXpath
     */
    public function it_can_parse_xpaths($xpath, $expected): void
    {
        $result = Xpath::xpath()->tryString($xpath);

        //self::assertTrue($result->isSuccess());
        self::assertSame($expected, $result->output());
    }

    /**
     * @test
     * @dataProvider provideInvalidXpaths
     */
    public function it_can_deal_with_invalid_xpaths($xpath): void
    {
        $result = Xpath::xpath()->tryString($xpath);

        self::assertTrue($result->isFail());
    }

    public function providesXpath()
    {
        yield [
            '//hello/world',
            ['//', 'hello', '/', 'world']
        ];

        yield [
            'hello/world',
            ['hello', '/', 'world']
        ];

        yield [
            'hello//world',
            ['hello', '//', 'world']
        ];
        yield [
            'namespaced:hello//world',
            ['namespaced:hello', '//', 'world']
        ];
        yield [
            'namespaced:hello//world[@attrib]',
            ['namespaced:hello', '//', 'world', ['@attrib', null, null]]
        ];
        yield [
            'namespaced:hello//world[@attrib=1]',
            ['namespaced:hello', '//', 'world', ['@attrib', '=', '1']]
        ];
        yield [
            'namespaced:hello//world[@attrib = 1]',
            ['namespaced:hello', '//', 'world', ['@attrib', '=', '1']]
        ];
        yield [
            'namespaced:hello//world[@attrib=1.1]',
            ['namespaced:hello', '//', 'world', ['@attrib', '=', '1.1']]
        ];
    }

    public function provideInvalidXpaths()
    {
        yield [''];
        yield ['///'];
        yield ['...'];
        yield ['namespaced:again:issue'];
    }
}
