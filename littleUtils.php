<?php
/**
 * Library of little and util functions.
 * See https://github.com/ppKrauss/php-little-utils
 */

/**
 * Check if is terminal or not.
 * @return boolean true when is client (terminal).
 */
function is_cli() {
  return (php_sapi_name() === 'cli'); // && empty($_SERVER['REMOTE_ADDR']
  //return ( empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv'])>0)
}


/**
 * Gets options from the Web ($_REQUEST) or terminal (command line argument list).
 * The terminal behaviour is similar to the standard getopt() function.
 * Conventions: only long and required options, "--opt=val", are allowed, and only "-h" short-option is offered. 
 * @param $opts associative array with the 'name'/default pairs.
 * @param $is_cli when not null reuses the is_cli() flag.
 * @param $req_header
 * @para $h help command convention.  'cmd' (default to be 'help') or '' to ignore.
 * @param $globalize boolean (true) to change globalizing behaviour.
 * @return array and (optional) globalized variables.
 */
function getopt_std($opts=NULL,$is_cli=NULL,$req_header='',$h='cmd',$globalize=true) {
   $autoWscOpts = array('html'=>'h', 'xml'=>'x', 'json'=>'j', 'plain'=>'t', ''=>''); // autodetect
   $r = array(); 
   if ($is_cli===NULL) $is_cli=is_cli();
   if ($opts===NULL || !is_array($opts)) 
      return NULL;
   elseif ($is_cli) {
      $a=array();
      foreach (array_keys($opts) as $k) $a[] = "$k:";
      $r0 = getopt($h?'h':'', $a);      
      if ($h && isset($r0['h'])) {
         unset($r0['h']);
         $r0[$h]='info'; // or 'help'
      }
   }
   foreach ($opts as $name => $dft) {
      $v = $is_cli?  (isset($r0[$name])? $r0[$name]: ''):  (isset($_REQUEST[$name])? $_REQUEST[$name]: '');
      $v = ($name=='urn')? trim($v): strtolower(trim($v)); // filter conventions
      $r[$name] = $v? $v: $dft;  // default convention
      if ($globalize)  $GLOBALS[$name] = $r[$name];
   } // for
   if (isset($r['wsc']) && $r['wsc']=='a') { // auto-detect by HTTP headers
    $r['wsc'] = $autoWscOpts[ parseHttpAccept($req_header['Accept']) ]; 
   }
   return $r;
} // func



// // // // // // // // // // // // // //
// // BEGIN:ARRAY_UTILS

/**
 * Reads entire CSV file into an array (or associative array or pair of header-content arrays).
 * Like build-in file() function, but to CSV handling. 
 * @param $opt not-null (can be empty) associative array of options (sep,head,assoc,limit,enclosure,escape)
 * @param $length same as in fgetcsv(). 
 * @param $context same as in fopen(). 
 * @return array (as head and assoc options).
 */
function file_csv($file, $opt=[], $length=0, resource $context=NULL) {
	$opt = array_merge(['sep'=>',', 'enclosure'=>'"', 'escape'=>"\\", 'head'=>false, 'assoc'=>false, 'limit'=>0], $opt);
	$header = NULL;
	$n=0; $nmax=(int)$opt['limit'];
	$lines = [];
	$h = fopen($file,'r',false,$context); // or $context? fopen(): fopen()
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
// // END:ARRAY_UTILS
// // // // // // // // // // // // //



// // // // // // // // // // // // // //
// // BEGIN:ARRAY_UTILS

/**
 * Get the commom start-string of two or more strings.  
 * The algorithm splits the string by its differences.
 * @version 1.1 of 2015-06-04
 * @param $a mix, string for final operation, or array iteration. 
 * @param $b string with probable commom root, or integer for array index.
 * @return string $root.
 * @see this function can feed the root for str_splitByRoot() function.
 *
 * USE CASE:
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

// // END:STRING_UTILS
// // // // // // // // // // // // //

