<?php

declare(strict_types=1);

namespace XpathReader\Reader\Matcher;

use XpathReader\Reader\Node\NodeSequence;

class OpeningElementMatcher implements MatcherInterface
{
    private string $xpath;

    /**
     * @var callable
     */
    private $whenMatched;

    public function __construct(string $xpath, callable $whenMatched)
    {
        $this->xpath = $xpath;
        $this->whenMatched = $whenMatched;
    }

    public function canHandle(NodeSequence $sequence): bool
    {
        return $sequence->matches($this->xpath);
    }

    public function handle(NodeSequence $sequence, string $xml): iterable
    {
        return ($this->whenMatched)($sequence, $xml);
    }
}
