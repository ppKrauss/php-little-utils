<?php
//
// See https://github.com/ppKrauss/php-little-utils
// MIT License Copyright (c) 2016 Peter Krauss
//

/**
 * Check if is a filename string, not a XML/HTML/markup string.
 * @return boolean true when is filename or path string.
 */
function isFile($input,$flenLimit=1000) {
	return strrpos($input,'<')==false && (!$flenLimit || strlen($input)<$flenLimit);
}

/**
 * Check if is terminal or not.
 * @return boolean true when is client (terminal).
 */
function is_cli() {
  return (php_sapi_name() === 'cli'); // && empty($_SERVER['REMOTE_ADDR']
  //return ( empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv'])>0)
}
