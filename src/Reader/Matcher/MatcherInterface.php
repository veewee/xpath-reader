<?php

declare(strict_types=1);

namespace XpathReader\Reader\Matcher;

use XpathReader\Reader\Node\NodeSequence;

interface MatcherInterface
{
    public function canHandle(NodeSequence $sequence): bool;
    public function handle(NodeSequence $sequence, string $xml): iterable;
}
