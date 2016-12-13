<?php
/**
 * Array functions.
 * @see https://github.com/ppKrauss/php-little-utils
 * @use check.php
 * @license MIT License Copyright (c) 2016 Peter Krauss
 */


/**
 * Standard "get array from CSV", file or CSV-string.
 * CSV conventions by default options of the build-in str_getcsv() function.
 * @param $f string file (.csv) with path or CSV string (with more tham 1 line).
 * @param $flenLimit integer 0 or limit of filename length (as in isFile function).
 * @return array of arrays.
 * @use isFile() at check.php
 */
function getCsv($f,$flenLimit=600) {
	return array_map(
		'str_getcsv', 
		isFile($f,$flenLimit,"\n")? file($f): explode($f,"\n") 
	);
}
 

/**
 * Get data (array of associative arrays) from CSV file, only the listed keys.
 * @param $f string file (.csv) with path or CSV string (with more tham 1 line). 
 * @param $flist array of column names, or NULL for "all columns".
 * @param $outJSON boolean true for JSON output. 
 * @param $flenLimit integer 0 or limit of filename length (as in isFile function).
 * @return mix JSON or array of associative arrays.
 */
function getCsvFields($f,$flist=NULL,$outJSON=false,$flenLimit=600) {
	$t = getCsv($f,$flenLimit);
	$thead = array_shift($t);

	$r = [];
	foreach($t as $x) {
		$a = array_combine($thead,$x);
		if ($flist===NULL)
			$r[] = $a;
		elseif (isset($a[$flist[0]])) {  // ~ array_column()
			$tmp = [];  // NEED OPTIMIZE WITH array_intersection!
			foreach ($flist as $g) $tmp[$g] = $a[$g];
			$r[] = $tmp;
		}
	}
	return $outJSON? json_encode($r): $r;
}


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
 * Standard "save array as CSV file".
 * CSV conventions by default options of the build-in fputcsv() function.
 * @param $f string filename (.csv) with path.
 * @param $a array (of arrays or of associative arrays).
 * @param $head array header.
 * @output file with CSV.
 */
function array_saveAsCsv($f,$a,$header=NULL,$isAssoc=true) {
	
	if ($header===NULL) {
		if ($isAssoc) 
			$header = array_keys($a[0]);
		else {
			$header = $a[0];
			unset($a[0]);
		}
	} //elseif ( $isAssoc && count($header) < count(array_keys($a[0])) ) 
	$fp = fopen($f, 'w');
	fputcsv($fp,$header);
	foreach($a as $idx=>$r0) {
		$r = [];
		if ($isAssoc) { foreach($header as $i) $r[] = isset($r0[$i])? $r0[$i]: ''; }
		else $r = $r0;
		fputcsv($fp,$r);
	}
	return 1;
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

/**
 * Get only specified key of an array of associative arrays.
 * @param $key string with the key.
 * @param $a array to be scanned.
 * @param $ignoreNulls boolean true for ignore nulls, falses and empties.
 * Hum... there are a native PHP funcion to do it?? reduce? map?
 */
function array_ofKey($key,$a,$ignoreNulls=true) {
  $r = [];
  foreach ($a as $x)
		if ( array_key_exists($key,$x)  &&  (!$ignoreNulls || $x[$key]) )
			$r[]=$x[$key];
  return $r;
}
