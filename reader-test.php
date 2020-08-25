<?php

use XpathReader\Reader\Handler\ExpressionsEvaluator;
use XpathReader\Reader\Handler\RecursiveReader;
use XpathReader\Reader\Handler\XmlImportingReader;
use XpathReader\Reader\Matcher\OpeningElementMatcher;
use XpathReader\Reader\XpathReader;

require_once __DIR__.'/vendor/autoload.php';

$reader = new XpathReader(
    new OpeningElementMatcher('actors', new ExpressionsEvaluator([
        'test' => 'actor',
        'something' => 'something',
    ])),
    new OpeningElementMatcher('actors', new RecursiveReader(
        new OpeningElementMatcher('actor', function ($sequence, $xml) {
            yield $xml;
        })
    )),
    new OpeningElementMatcher('singers', new XmlImportingReader(
        function (string $xml) {
            return <<<EOXML
	<singers>
		<singer id="4">Tom Waits</singer>
		<singer id="5">B.B. King</singer>
		<singer id="6">Ray Charles</singer>
	</singers>
EOXML;

        },
        new OpeningElementMatcher('singer', function ($sequence, $xml) {
            yield $xml;
        })
    ))
);
$iterator = $reader->readXml(<<<EOXML
<root xmlns:foo="http://www.foo.org/" xmlns:bar="http://www.bar.org">
	<actors>
		<actor id="1">Christian Bale</actor>
		<actor id="2">Liam Neeson</actor>
		<actor id="3">Michael Caine</actor>
		<just><something>Toon</something></just>
	</actors>
	<foo:singers>
		<foo:singer id="4">Tom Waits</foo:singer>
		<foo:singer id="5">B.B. King</foo:singer>
		<foo:singer id="6">Ray Charles</foo:singer>
	</foo:singers>
</root>
EOXML
);

var_dump(iterator_to_array($iterator, false));