<?php
/**
 * Getopt functions.
 * @see https://github.com/ppKrauss/php-little-utils
 * @license MIT License Copyright (c) 2016 Peter Krauss
 */
 
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
