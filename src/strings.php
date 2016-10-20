<?php
/**
 * String functions.
 * @see https://github.com/ppKrauss/php-little-utils
 * @license MIT License Copyright (c) 2016 Peter Krauss
 * @uses  isFile() at basiChecks.php
 */

/**
 * Get the commom start-string of two or more strings.
 * The algorithm splits the string by its differences.
 * @version 1.1 of 2015-06-04
 * @see this function can feed the root for str_splitByRoot() function.
 *
 * @param $a mix, string for final operation, or array iteration.
 * @param $b string with probable commom root, or integer for array index.
 * @param $minLen integer minimal length.
 * @param $retFamily boolean true for return family.
 * @return string $root.
 *
 * @example
 *	$root=str_getRoot($DOIs,1,5);
 *	foreach ($DOIs as $doi) {
 *		$dif = str_splitByRoot($doi, $root, ' * ');
 *		print "\n-- $doi \t= $dif";
 *	}
 */
function str_getRoot($a,$b='',$minLen=1,$retFamily=false) {
	if (is_array($a)) { // $b is a seed.
		if (is_integer($b))
			$b = $a[$b];
        if (!$b && isset($a[0])) $b=$a[0];
		$family = array();
		foreach ($a as $item) {
			if (is_array($item)) die("array of array is invalid");
			$r = str_getRoot($item,$b);
			if (strlen($r)>=$minLen) {
				if ($retFamily) $family[]=$item;
				if ($r!=$b) $b=$r;
			} //if
		} // for
		if ($retFamily) {
			array_unshift($family,$b);
			return $family;
		} else
			return $b;
	} else {  // $b is a root
	    $rooLen = strspn($b ^ $a, "\0");
	    return substr($a,0,$rooLen);
	}
}

/**
 * Splits a string by its root and sufix.
 * @param $str string input.
 * @param $root prefix string (can be empty or NULL as no-root).
 * @param $retSep non-empty for string return.
 * @return mix array(rootFlag,suffix) or string.
 * @see http://codereview.stackexchange.com/q/92601/24276
 */
function  str_splitByRoot($str, $root, $retSep='') {
	$r = ($root && strpos($str,$root)===0) ?
		array($root, substr($str, strlen($root)) ) :
		array( '', $str )
	;
	return $retSep? "$r[0]$retSep$r[1]": $r;
}