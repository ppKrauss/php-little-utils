<?php

/**
 * A generic "util library" for strings, and a helper for XsLib and others.
 */
class str {

	/**
	 * Change case: uppercase|capitalize|lowercase (or MB_CASE_* constants).
	 * A wrapper for mb_convert_case().
	 */
	static function chgCase($s,$case) {
		static $mapCase = array('uppercase'=>MB_CASE_UPPER, 'capitalize'=>MB_CASE_TITLE, 'lowercase'=> MB_CASE_LOWER);
		if (!is_int($case))
			$case=$mapCase[$case];
		return mb_convert_case($s, $case, 'UTF-8');
	}


	/**
	 * Detecta idioma (lang) da string, através do GoogleAPIs
	 * Ver https://developers.google.com/translate/v2/using_rest#detect-query-params
	 * Adaptar depois ao uso de timeout (!) http://stackoverflow.com/questions/3500527/php-soapclient-timeout
	 * @param string $s texto a ser analisado.
	 * @return array associativa {'language':(2 letras), 'isReliable':(boolean), 'confidence': (float entre 0 e 1)}
	 */
	static function langDetect($s) {
		$s = trim($s);
	 static $url='https://www.googleapis.com/language/translate/v2/detect?key=AIzaSyDWbhV0XdKvzXxvmk-TAy3-8mkW8pMfdK0&prettyprint=false&q=';
		$r = @file_get_contents($url.urlencode($s));
		// AQUI REQUER try com "time limit" ...
		if ($r) {
			$r = json_decode( $r,  true );
			return $r['data']['detections'][0][0];
		} else
			return '';
	}

	/**
	 * Validate an email address.
	 * @param $email  email address (raw input)
	 * @return true if the email address has the email address format and the domain exists.
	 * @example: VALIDO, peter@cdcc.usp.br;   INVALIDO, peter@cdck.usalkdjskdsp.br
	 */
	static function validEmail($email,$checkOnline=true)
	{
	   $isValid = true;
	   $atIndex = strrpos($email, "@");
	   if (is_bool($atIndex) && !$atIndex)
	      $isValid = false;
	   else {
	      $domain = substr($email, $atIndex+1);
	      $local = substr($email, 0, $atIndex);
	      $localLen = strlen($local);
	      $domainLen = strlen($domain);
	      if ($localLen < 1 || $localLen > 64)
			 $isValid = false;
	      else if ($domainLen < 1 || $domainLen > 255)
			 $isValid = false;
	      else if ($local[0] == '.' || $local[$localLen-1] == '.')
			 $isValid = false;
	      else if (preg_match('/\\.\\./', $local))
			 $isValid = false;
	      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
			 $isValid = false;
	      else if (preg_match('/\\.\\./', $domain))
			 $isValid = false;
	      else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))) {
			if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
				$isValid = false;
	      }
	      if (  $isValid && $checkOnline ) {
			if (! (checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))  )
			$isValid = false;
	      }
	   }
	   return $isValid;
	}

	/**
	 * Converte string UTF8 em "primera maisúscula".
	 *
	 * @param $str string a ser convertida (precisa chegar com TRIM E STRIPTAGS!).
	 * @param $lower_str_end boolean(false): flag permitindo uso da hipotese de "todas tags minusculas".
	 * @return XML com apenas conteudo não-tag convertido. Valores dos atributos não são submetidos.
	 */
	static function mb_ucfirst($str, $lower_str_end=false) {
	      static $enc='UTF-8'; // ou
	      $first_letter = mb_strtoupper(mb_substr($str, 0, 1, $enc), $enc);
	      $str_end = "";
	      if ($lower_str_end) {
		$str_end = mb_strtolower(mb_substr($str, 1, mb_strlen($str, $enc), $enc), $enc);
	      }
	      else {
		$str_end = mb_substr($str, 1, mb_strlen($str, $enc), $enc);
	      }
	      $str = $first_letter . $str_end;
	      return $str;
	}

	/**
	 * Adaptação de mb_ucfirst para percorrer frase e ignorar preposições.
	 */
	static function mb_ucfirst_properName($str, $lower_str_end=false) {
		$sep_list = array();
		$str = preg_replace_callback(
			'#([^\s\.,\-;]+)([\s\.,\-;]+)#uis',
			function ($m) use ($lower_str_end) {
				static $enc='UTF-8';
				static $stoplist='|de|do|da|dos|das|van|der|del|';
				$w = $m[1];
				$lw = mb_strtolower($w,$enc);
				if (strpos($stoplist,"|$lw|")!==false)
					$ss = $lw;
				else
					$ss = mb_ucfirst($w,$lower_str_end);
				return "$ss$m[2]"; //-$m[1];
			},
			$str
		);
		return $str;
	}

	/**
	 * Detecta "case" da string UTF8.
	 *
	 * @param $text string a ser analisada.
	 * @return upp|low|ucf1|camel.
	 */
	static function caseDetect($text) {
		static $enc='UTF-8';

		if (mb_strtoupper($text,$enc)==$text)
			return 'upp';
		elseif (mb_strtolower($text,$enc)==$text)
			return 'low';
		elseif (mb_ucfirst($text,1)==$text) // 1st upp e demais lower
			return 'ucf1';
		else
			return 'camel';
	}



	/**
	 * LIXO.
	 */
	static function dec2roman_old($x) {
		return str_replace(array(10,11,12,13,14,15,16,17,18,19,  1,2,3,4,5,6,7,8,9),
				array('X','XI','XII','XIII','XIV', 'XV','XVI','XVII','XVIII','XIX',
					   'I','II','III','IV',     'V','VI','VII','VIII',   'IX'),
				$x
		);
	}

	/**
	 * Decimal to Roman integers convertion.
	 *
	 * @param $f a decimal integer number in the range 0-3999.
	 * @return the upper-case roman, or false if either $f is not a real number, or $f is out of range.
	 */
	static function dec2roman($f) {
		// Return

		// Define the roman figures:
		static $roman = array('M' => 1000, 'D' => 500, 'C' => 100, 'L' => 50, 'X' => 10, 'V' => 5, 'I' => 1);
		static $falseRoman = 		array('VIV','LXL','DCD');
		static $falseRoman_correction =	array('IX', 'XC', 'CM');

		if(!is_numeric($f) || $f > 3999 || $f <= 0) return false;

		// Calculate the needed roman figures:
		foreach($roman as $k => $v)
			if(($amount[$k] = floor($f / $v)) > 0) $f -= $amount[$k] * $v;

		// Build the string:
		$return = '';
		foreach($amount as $k => $v) {
		    $return .= $v <= 3 ? str_repeat($k, $v) : $k . $old_k;
		    $old_k = $k;
		}
		return str_replace($falseRoman, $falseRoman_correction, $return);
	}

	/**
	 * Roman to Decimal integers convertion.
	 * NOTE: VIV=IX and LXL=XC, etc. are not error, so roman2dec(dec2roman($x)) can correct invalid romans.
	 * @param $roman a valid (case insensitive) roman number.
	 * @return the roman decimal value, or error as false (input decimal) or 0 (non-roman).
	 */
	static function roman2dec($roman) {
		static $romans = array(
		    'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100,  'XC' => 90,
		    'L' => 50,   'XL' => 40,  'X' => 10,  'IX' => 9,   'V' => 5,    'IV' => 4,
		    'I' => 1,
		);
		if(is_numeric($roman)) return false; // regex valid?
		$dec = 0;
		$roman = strtoupper( trim($roman) ); // sanitize input, ignore case
		foreach ($romans as $key => $value)
		    while (strpos($roman, $key) === 0) {
			$dec += $value;
			$roman = substr($roman, strlen($key));
		    }
		return $dec;
	}
} //class str
