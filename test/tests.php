<?php
/**
 * Test and demo for simpleXml2JsonML() function.
 * USE: php tests.php |more
 */

include dirname(__DIR__).'/src/simpleXml2JsonML.php';

if ( php_sapi_name() != 'cli' ) header('Content-Type:text/plain');
$xml = '
	<states x="1">
	    <state y="123">Alabama</state>
			My name is <b>John</b> Doe
	    <state>Alaska</state>
	</states>
';
$xmlTestFile = dirname(__DIR__).'/test/test.xml'; // from http://www.lexml.gov.br/vocabulario/tipoConteudo.rdf.xml
?>

v1.1 tests and demo
string XML = <?=$xml?>

== Use jsonML conventions and return JSON  ==

=== XML from string ===
$j = xml2json($xml,false);
<?php
echo $xml;
  $j = xml2json($xml,false);
  echo $j;
?>

=== XML from string in a XPath===
$j = xpath2jsonML($xml,'//state',false);
<?php
  $j = xpath2jsonML($xml,'//state',false);
  echo $j;
?>


=== XML from remote file (URL) ===
$j = xml2json($xmlTestFile,false);
<?php
  $j = xml2json($xmlTestFile,false);
  echo $j;
?>


=== XML from local file ===
$j = xml2json($xmlTestFile,false);
<?php
  $j = xml2json($xmlTestFile,false);
  echo $j;
?>

== Use jsonML conventions and return PHP-array  ==

=== XML from string ===
$a = xml2json($xml);
<?php
  $a = xml2json($xml);
  var_dump($a);
?>

=== XML from DOM ===
$dom = DOMDocument::loadXML($xml);
$a = xml2json($dom); echo "Ok, see root: $a[0].";
<?php
  $dom = DOMDocument::loadXML($xml);
  $a = xml2json($dom); echo "Ok! see root: $a[0].";
?>


=== XML from string and traverse ===
$a = xml2json($xml); foreach($a['states'] as $r) var_dump($r);
<?php
  echo "\ntag-root=$a[0]\n";
  foreach($a as $r)
    var_dump($r);
?>


== Use jsonML conventions and return PHP-array with objects  ==

=== XML from string ===
$a = xml2json($xml,true,true,false);
<?php
  $a = xml2json($xml,true,true,false);
  var_dump($a);
?>
