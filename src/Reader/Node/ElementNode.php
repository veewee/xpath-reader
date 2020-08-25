<?php

declare(strict_types=1);

namespace XpathReader\Reader\Node;

class ElementNode
{
    public int $position;
    public $name;
    public $localName;
    public $namespace;
    public $namespaceAlias;
    public $arguments = [];
}
