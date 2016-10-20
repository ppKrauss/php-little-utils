<?php
/**
 * Basic checks functions.
 * @see https://github.com/ppKrauss/php-little-utils
 * @license MIT License Copyright (c) 2016 Peter Krauss
 */

/**
 * Check if is a filename string, not a XML/HTML/markup string.
 * @param $input string of filename or markup code.
 * @param $flenLimit integer 0 or limit of filename length.
 * @return boolean true when is filename or path string, false when markup.
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
