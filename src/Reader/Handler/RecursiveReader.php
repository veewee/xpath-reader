<?php

declare(strict_types=1);

namespace XpathReader\Reader\Handler;

use XpathReader\Reader\Matcher\MatcherInterface;
use XpathReader\Reader\Node\NodeSequence;
use XpathReader\Reader\XpathReader;

class RecursiveReader
{
    /**
     * @var MatcherInterface[]
     */
    private array $matchers;

    public function __construct(MatcherInterface ... $matchers)
    {
        $this->matchers = $matchers;
    }

    public function __invoke(NodeSequence $sequence, string $xml): iterable
    {
        $reader = new XpathReader(...$this->matchers);

        yield from $reader->readXml($xml, $sequence->pop());
    }
}
