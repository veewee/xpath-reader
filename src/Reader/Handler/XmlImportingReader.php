<?php

declare(strict_types=1);

namespace XpathReader\Reader\Handler;

use XpathReader\Reader\Matcher\MatcherInterface;
use XpathReader\Reader\Node\NodeSequence;
use XpathReader\Reader\XpathReader;

class XmlImportingReader
{
    /**
     * @var MatcherInterface[]
     */
    private array $matchers;

    /**
     * @var callable(string): string
     */
    private $importedXmlResolver;

    public function __construct(callable $importedXmlResolver, MatcherInterface ... $matchers)
    {
        $this->matchers = $matchers;
        $this->importedXmlResolver = $importedXmlResolver;
    }

    public function __invoke(NodeSequence $sequence, string $xml): iterable
    {
        $reader = new XpathReader(...$this->matchers);

        yield from $reader->readXml(($this->importedXmlResolver)($xml), $sequence->pop());
    }
}
