<?php
/**
 * Test and demo for xml2json() functions. Generates xml2json.md guide.
 *
 * @example php test/xml2json_test.php | more
 * @example php test/xml2json_test.php | diff xml2json.md -
 */

include dirname(__DIR__).'/src/basiChecks.php';
include dirname(__DIR__).'/src/xml2json.php';

if ( !is_cli() ) header('Content-Type:text/plain');
$xml = '
	<states x="1">
	    <state y="123">Alabama</state>
			My name is <b>John</b> Doe
	    <state>Alaska</state>
	</states>
';
$xmlTestFile = dirname(__DIR__).'/test/test.xml'; // from http://www.lexml.gov.br/vocabulario/tipoConteudo.rdf.xml
?>
---
title: XML-JSON convertion, tests and demo
version: 1.1
layout: page
---

Demo for [xml2json](src/xml2json.php) functions, see [its generator](test/xml2json_test.php).


# Demo Inputs
string XML =
```xml<?=$xml?>
```
file = [test.xml](test.xml)

# Use jsonML conventions and return JSON  #
Converts XML file (or XML string or DOMDocument) to JSON or to PHP array.
Use **[jsonML](https://github.com/mckamey/jsonml) convertion**.

### XML from string ###
$j = xml2json($xml,false);
```json
<?php
  $j = xml2json($xml,false);
  echo $j.PHP_EOL;
?>
```
### XML from string in a XPath ###
$j = xpath2jsonML($xml,'//state',false);
```json
<?php
  $j = xpath2jsonML($xml,'//state',false);
  echo $j.PHP_EOL;
?>
```

### XML from remote file (URL) ###
$j = xml2json($xmlTestUrl,false);


### XML from local file ###
$j = xml2json($xmlTestFile,false);
```json
<?php
  $j = xml2json($xmlTestFile,false);
  echo $j;
?>
```

# Use jsonML conventions and return PHP-array #

### XML from string ###
$a = xml2json($xml);
```php
<?php
  $a = xml2json($xml);
  var_export($a);
?>
```

### XML from DOM ###
$dom = DOMDocument::loadXML($xml);
$a = xml2json($dom); echo "Ok, see root: $a[0].";
```
<?php
  $dom = DOMDocument::loadXML($xml);
  $a = xml2json($dom); echo "Ok! see root: $a[0].";
?>
```

### XML from string and traverse ###
$a = xml2json($xml); foreach($a['states'] as $r) var_dump($r);
```
<?php
  echo "\ntag-root=$a[0]\n";
  foreach($a as $r)
    var_dump($r);
?>
```

# Use jsonML conventions and return PHP-array with objects #

### XML from string ###
$a = xml2json($xml,true,true,false);
```
<?php
  $a = xml2json($xml,true,true,false);
  var_dump($a);
?>
```
