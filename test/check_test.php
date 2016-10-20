<?php
/**
 * Test and demo for basic check functions.
 *
 * @example php test/check_test.php | more
 * @example php test/check_test.php | diff check.md -
 */

include dirname(__DIR__).'/src/check.php';

if ( !is_cli() ) header('Content-Type:text/plain');
$xmlFrag = 'this/is/a/XML/fragment <b>ok</b>';
$xmlComplete = file_get_contents(dirname(__DIR__).'/test/test.xml');
$filename_long = dirname(__DIR__).'/src/check.php';
$filename_short = 'hello.txt';
?>
---
title: basic check, tests and demo
version: 1.0
layout: page
---

Demo for basic [check](src/check.php) functions, see [its generator](test/check_test.php).

# Demo Inputs
```php
$xmlFrag = '<?php echo $xmlFrag; ?>';
$filename_long // this local file
$filename_short = '<?php echo $filename_short; ?>';
$xmlComplete = file_get_contents('test.xml');
```

# Checks  #

### Running this script from terminal? ###
* is_cli() = <?php echo is_cli(); ?>


### String is a markup filename or a markup itself? ###

* isFile($xmlFrag) = <?php echo isFile($xmlFrag); ?>;
* isFile($xmlFrag,10) = <?php echo isFile($xmlFrag,10); ?>;
* isFile($filename_short) = <?php echo isFile($filename_short); ?>;
* isFile($filename_short,10) = <?php echo isFile($filename_short,10); ?>;
* isFile($filename_long,10) = <?php echo isFile($filename_long,10); ?>;
* isFile($filename_long) = <?php echo isFile($filename_long); ?>;
* isFile($xmlComplete) = <?php echo isFile($xmlComplete); ?>;
