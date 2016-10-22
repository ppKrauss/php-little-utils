---
title: XML-JSON convertion, tests and demo
version: 1.1
layout: page
---

Demo for [xml2json](src/xml2json.php) functions, see [its generator](test/xml2json_test.php).


# Demo Inputs
string XML =
```xml
	<states x="1">
	    <state y="123">Alabama</state>
			My name is <b>John</b> Doe
	    <state>Alaska</state>
	</states>
```
file = [test.xml](test.xml)

# Use jsonML conventions and return JSON  #
Converts XML file (or XML string or DOMDocument) to JSON or to PHP array.
Use **[jsonML](https://github.com/mckamey/jsonml) convertion**.

### XML from string ###
$j = xml2json($xml,false);
```json
["states",{"x":"1"},"\n\t    ",["state",{"y":"123"},"Alabama"],"\n\t\t\tMy name is ",["b","John"]," Doe\n\t    ",["state","Alaska"],"\n\t"]
```
### XML from string in a XPath ###
$j = xpath2jsonML($xml,'//state',false);
```json
["_DOMNodeList",["state",{"y":"123"},"Alabama"],["state","Alaska"]]
```

### XML from remote file (URL) ###
$j = xml2json($xmlTestUrl,false);


### XML from local file ###
$j = xml2json($xmlTestFile,false);
```json
["rdf:RDF",{"xml:base":"http://www.lexml.gov.br/vocabulario/br/tipoConteudo#"},"\n",["skos:ConceptScheme",{"rdf:about":"tipoConteudo"},"\n",["dc:title","Vocabulário de tipo da expressão do Conteúdo"],"\n",["dc:description","Vocabulário de tipo da expressão do Conteúdo."]],"\n\n",["skos:Concept",{"rdf:about":"texto","rdf:id":"1"},"\n",["skos:prefLabel",{"xml:lang":"pt-BR"},"Texto"],"\n",["skos:inScheme",{"rdf:resource":"#tipoConteudo"}],"\n"],"\n\n",["skos:Concept",{"rdf:about":"imagem","rdf:id":"2"},"\n",["skos:prefLabel",{"xml:lang":"pt-BR"},"Imagem"],"\n",["skos:inScheme",{"rdf:resource":"#tipoConteudo"}],"\n"],"\n\n",["skos:Concept",{"rdf:about":"imagem.movimento","rdf:id":"3"},"\n",["skos:prefLabel",{"xml:lang":"pt-BR"},"Imagem em Movimento"],"\n",["skos:inScheme",{"rdf:resource":"#tipoConteudo"}],"\n"],"\n\n",["skos:Concept",{"rdf:about":"musica","rdf:id":"4"},"\n",["skos:prefLabel",{"xml:lang":"pt-BR"},"Música"],"\n",["skos:inScheme",{"rdf:resource":"#tipoConteudo"}],"\n"],"\n\n",["skos:Concept",{"rdf:about":"notacao.musical","rdf:id":"5"},"\n",["skos:prefLabel",{"xml:lang":"pt-BR"},"Notação Musical"],"\n",["skos:inScheme",{"rdf:resource":"#tipoConteudo"}],"\n"],"\n\n",["skos:Concept",{"rdf:about":"texto.falado","rdf:id":"6"},"\n",["skos:prefLabel",{"xml:lang":"pt-BR"},"Texto Falado"],"\n",["skos:inScheme",{"rdf:resource":"#tipoConteudo"}],"\n"],"\n"]```

# Use jsonML conventions and return PHP-array #

### XML from string ###
$a = xml2json($xml);
```php
array (
  0 => 'states',
  1 => 
  array (
    'x' => '1',
  ),
  2 => '
	    ',
  3 => 
  array (
    0 => 'state',
    1 => 
    array (
      'y' => '123',
    ),
    2 => 'Alabama',
  ),
  4 => '
			My name is ',
  5 => 
  array (
    0 => 'b',
    1 => 'John',
  ),
  6 => ' Doe
	    ',
  7 => 
  array (
    0 => 'state',
    1 => 'Alaska',
  ),
  8 => '
	',
)```

### XML from DOM ###
$dom = DOMDocument::loadXML($xml);
$a = xml2json($dom); echo "Ok, see root: $a[0].";
```
Ok! see root: states.```

### XML from string and traverse ###
$a = xml2json($xml); foreach($a['states'] as $r) var_dump($r);
```

tag-root=states
string(6) "states"
array(1) {
  ["x"]=>
  string(1) "1"
}
string(6) "
	    "
array(3) {
  [0]=>
  string(5) "state"
  [1]=>
  array(1) {
    ["y"]=>
    string(3) "123"
  }
  [2]=>
  string(7) "Alabama"
}
string(15) "
			My name is "
array(2) {
  [0]=>
  string(1) "b"
  [1]=>
  string(4) "John"
}
string(10) " Doe
	    "
array(2) {
  [0]=>
  string(5) "state"
  [1]=>
  string(6) "Alaska"
}
string(2) "
	"
```

# Use jsonML conventions and return PHP-array with objects #

### XML from string ###
$a = xml2json($xml,true,true,false);
```
array(9) {
  [0]=>
  string(6) "states"
  [1]=>
  object(stdClass)#6 (1) {
    ["x"]=>
    string(1) "1"
  }
  [2]=>
  string(6) "
	    "
  [3]=>
  array(3) {
    [0]=>
    string(5) "state"
    [1]=>
    object(stdClass)#5 (1) {
      ["y"]=>
      string(3) "123"
    }
    [2]=>
    string(7) "Alabama"
  }
  [4]=>
  string(15) "
			My name is "
  [5]=>
  array(2) {
    [0]=>
    string(1) "b"
    [1]=>
    string(4) "John"
  }
  [6]=>
  string(10) " Doe
	    "
  [7]=>
  array(2) {
    [0]=>
    string(5) "state"
    [1]=>
    string(6) "Alaska"
  }
  [8]=>
  string(2) "
	"
}
```
