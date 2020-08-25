<?php

declare(strict_types=1);

namespace XpathReader\Reader\Handler;

use XpathReader\Reader\Matcher\OpeningElementMatcher;
use XpathReader\Reader\Node\NodeSequence;
use XpathReader\Reader\XpathReader;

class ExpressionsEvaluator
{
    private array $expressions;

    public function __construct(array $expressions)
    {
        $this->expressions = $expressions;
    }

    public function __invoke(NodeSequence $sequence, string $xml): iterable
    {
        $reader = new XpathReader(
            ...array_map(
                function (string $xpath, string $name) {
                    return new OpeningElementMatcher($xpath, function (NodeSequence $sequence, string $xml) use ($name) {
                        yield $name => (string) \simplexml_load_string($xml);
                    });
                },
                $this->expressions,
                array_keys($this->expressions)
            )
        );

        yield from $reader->readXml($xml, $sequence->pop());
    }
}
