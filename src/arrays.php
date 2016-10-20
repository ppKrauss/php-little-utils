<?php
/**
 * Array functions.
 * @see https://github.com/ppKrauss/php-little-utils
 * @license MIT License Copyright (c) 2016 Peter Krauss
 */

/**
 * Reads entire CSV file into an array (or associative array or pair of header-content arrays).
 * Like build-in file() function, but to CSV handling.
 * @param $file string filename.
 * @param $opt not-null (can be empty) associative array of options (sep,enclosure,escape,head,assoc,limit)
 * @param $length integer same as in fgetcsv().
 * @param $context resource same as in fopen().
 * @return array (as head and assoc options).
 */
function file_csv($file, $opt=[], $length=0, resource $context=NULL) {
	$opt = array_merge(['sep'=>',', 'enclosure'=>'"', 'escape'=>"\\", 'head'=>false, 'assoc'=>false, 'limit'=>0], $opt);
	$header = NULL;
	$n=0; $nmax=(int)$opt['limit'];
	$lines = [];
	$h = $context? fopen($file,'r',false,$context):  fopen($file,'r');
	while( $h && !feof($h) && (!$nmax || $n<$nmax) )
		if ( ($r=fgetcsv($h,$length,$opt['sep'],$opt['enclosure'],$opt['escape'])) && $n++>0 )
			$lines[] = $opt['assoc']? array_combine($header,$r): $r;
		elseif ($n==1)
			$header = $r;
	return $opt['head']? array($header,$lines): $lines;
}


/**
 * Joins a set of key-value pairs by $pairSep, and all pairs of the set by $sep.
 * NOTE: assoc_join($a,'&') is similar to http_build_query($a).
 * @param $a mix NULL or array (handdled as reference) to be joined.
 * @param $sep string (default '; ') separator in the final string.
 * @param $pairSep string (default '=') pair separator (joins key-val pair).
 * @return mix NULL if $a is NULL, string if $a is array.
 */
function assoc_join(&$a,$sep='; ',$pairSep='=') {
	return is_array($a)?
		join($sep, array_map(
			function($key) use ($a,$pairSep) {
				$key = trim($key);
				return "$key$pairSep{$a[$key]}";
			},
			array_keys($a)
		)):
		NULL;
}

/**
 * Replaces or appends array. On key-conflicts, use the appended values.
 * @param $a mix NULL or array (handdled as reference) to be changed.
 * @param $append mix, string in the form "key=value", or associative array.
 */
function assoc_merge(&$a,$append) {
	if ( is_string($append) && count($append=explode('=',$append)) )
		$append = array($append[0]=>$append[1]);
	elseif (!is_array($append))
		$append = array();
	$a = ($a===NULL)? $append: array_merge($append,$a);
}

/**
 * Rename keys of an associative array. On key-conflicts, use the flag to decide.
 * @param $a mix NULL or array (handdled as reference) to be changed.
 * @param $rename associative array with the pairs oldKey-newKey.
 * @param $renameOverride boolean (default true) to decide override on conflicts with new keys.
 */
function assoc_rename(&$a, $rename, $renameOverride=true) {
	if (!is_array($a) || !is_array($rename))
		return false;
	foreach ($rename as $key=>$newKey)
		if ( array_key_exists($key, $a) && ($renameOverride || !array_key_exists($newKey, $a)) ) {
			$a[$newKey] = $a[$key];
			unset($a[$key]);
		}
	return true;
} // func

/**
 * Removes key-value pairs from array, by keys.
 * @param $a mix NULL or array (handdled as reference) to be changed.
 * @param $keys mix, string with the key, or array of keys.
 */
function assoc_unset(&$a,$keys) {
	if ( $a!==NULL ) {
		if (!is_array($keys))
			$keys=array($keys);
		foreach ($keys as $key) if (array_key_exists($key,$a))
			unset($a[$key]);
	}
}