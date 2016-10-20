---
title: basic check, tests and demo
version: 1.0
layout: page
---

Demo for basic [check](src/check.php) functions, see [its generator](test/check_test.php).

# Demo Inputs
$xmlFrag = 'this/is/a/XML/fragment <b>ok</b>';
$filename_long // this local file
$filename_short = 'hello.txt';
$xmlComplete = file_get_contents('test.xml');


# Checks  #

### Running this script from terminal? ###
* is_cli() = 1

### String is a markup filename or a markup itself? ###

* isFile($xmlFrag) = ;
* isFile($xmlFrag,10) = ;
* isFile($filename_short) = 1;
* isFile($filename_short,10) = 1;
* isFile($filename_long,10) = ;
* isFile($filename_long) = 1;
* isFile($xmlComplete) = ;
