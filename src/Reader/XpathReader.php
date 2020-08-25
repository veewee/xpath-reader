<?php

declare(strict_types=1);

namespace XpathReader\Reader;

use XMLReader;
use XpathReader\Reader\Matcher\ClosingElementMatcher;
use XpathReader\Reader\Matcher\MatcherInterface;
use XpathReader\Reader\Matcher\OpeningElementMatcher;
use XpathReader\Reader\Node\ElementNode;
use XpathReader\Reader\Node\AttributeNode;
use XpathReader\Reader\Node\NodeSequence;

class XpathReader
{
    private array $matchers;

    public function __construct(MatcherInterface ... $matchers)
    {
        $this->matchers = $matchers;
    }

    public function readXml(string $xml, NodeSequence $sequence = null): iterable
    {
        $sequence ??= new NodeSequence();
        $reader = new XMLReader();
        $reader->XML($xml);

        return $this->parse($reader, $sequence);
    }

    private function parse(XMLReader $reader, NodeSequence $sequence): iterable
    {
        $depth = 0;
        $siblingCount = [];

        while($reader->read()){
            switch ($reader->nodeType) {
                case XMLReader::END_ELEMENT:
                    unset($siblingCount[$depth]);
                    $depth--;
                    $sequence = $sequence->pop();
                    break;
                case XMLReader::ELEMENT:
                    $siblingsCount[$depth] = isset($siblingsCount[$depth]) ? ($siblingsCount[$depth]+1) : 1;
                    $position = $siblingsCount[$depth];
                    $depth++;

                    $element = new ElementNode();
                    $element->position = $position;
                    $element->name = $reader->name;
                    $element->localName = $reader->localName;
                    $element->namespace = $reader->namespaceURI;
                    $element->namespaceAlias = $reader->prefix;

                    while($reader->moveToNextAttribute()) {
                        $attribute = new AttributeNode();
                        $attribute->name = $reader->name;
                        $attribute->localName = $reader->localName;
                        $attribute->namespaceAlias = $reader->prefix;
                        $attribute->namespace = $reader->namespaceURI;
                        $attribute->value = $reader->value;
                        $element->arguments[] = $attribute;
                    }

                    $sequence = $sequence->append($element);
                    yield from $this->runMatchers(OpeningElementMatcher::class, $sequence, $reader);
                    break;
            }
        }
    }

    private function runMatchers(string $type, NodeSequence $sequence, XMLReader $reader): iterable
    {
        foreach ($this->matchers as $matcher) {
            if ($matcher instanceof $type) {
                if ($matcher->canHandle($sequence)) {
                    yield from $matcher->handle($sequence, $reader->readOuterXml());
                }
            }
        }
    }
}
