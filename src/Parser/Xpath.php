<?php

declare(strict_types=1);

namespace XpathReader\Parser;

use Verraes\Parsica\Parser;
use function Verraes\Parsica\alphaNumChar;
use function Verraes\Parsica\atLeastOne;
use function Verraes\Parsica\between;
use function Verraes\Parsica\char;
use function Verraes\Parsica\choice;
use function Verraes\Parsica\collect;
use function Verraes\Parsica\either;
use function Verraes\Parsica\float;
use function Verraes\Parsica\integer;
use function Verraes\Parsica\optional;
use function Verraes\Parsica\some;
use function Verraes\Parsica\space;
use function Verraes\Parsica\string;
use function Verraes\Parsica\zeroOrMore;

final class Xpath
{
    private function __construct()
    {
    }

    public static function node(): Parser
    {
        return choice(
            Xpath::nodeName(),
            Xpath::parentNode(),
            Xpath::currentNode(),
            Xpath::attribute()
        );
    }

    public static function separators(): Parser
    {
        return choice(
            Xpath::somewhere(),
            Xpath::nextNode()
        );
    }

    public static function nodeName(): Parser
    {
        $nameOrWildcard = either(
            Xpath::wildcard(),
            atLeastOne(alphaNumChar())
        );

        return choice(
            $nameOrWildcard->append(char(':'))->append($nameOrWildcard),
            $nameOrWildcard
        );
    }

    public static function wildcard(): Parser
    {
        return char('*');
    }

    public static function nextNode(): Parser
    {
        return char('/');
    }

    public static function somewhere(): Parser
    {
        return string('//');
    }

    public static function currentNode(): Parser
    {
        return char('.');
    }

    public static function parentNode(): Parser
    {
        return string('..');
    }

    public static function attribute(): Parser
    {
        return char('@')->append(atLeastOne(alphaNumChar()));
    }

    public static function ws(): Parser
    {
        return zeroOrMore(space());
    }

    public static function expression(): Parser
    {
        return collect(
            choice(
                Xpath::node(),
                Xpath::attribute()
            ),
            optional(self::operators()),
            optional(either(float(), integer()))
        );
    }

    public static function operators(): Parser
    {
        return self::ws()->followedBy(
            choice(
                char('|'),
                char('+'),
                char('-'),
                char('*'),
                string('div'),
                string('!='),
                string('>='),
                string('<='),
                char('='),
                char('<'),
                char('>'),
                string('or'),
                string('and'),
                string('mod')
            )
        )->thenIgnore(self::ws());
    }

    public static function betweenBrackets(): Parser
    {
        return between(
            char('['),
            char(']'),
            Xpath::expression()
        );
    }

    public static function xpath(): Parser
    {
        return some(choice(
            Xpath::separators(),
            Xpath::node(),
            Xpath::betweenBrackets()
        ));
    }
}
