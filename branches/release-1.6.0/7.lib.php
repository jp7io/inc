<?
/**
 * JP7's PHP Functions 
 * 
 * Contains the main custom functions and classes
 * @author JP7
 * @copyright Copyright 2002-2008 JP7 (http://jp7.com.br)
 * @version 1.10 (2008/06/16)
 * @category JP7
 * @package 7lib
 */

/**
 * In case $_SERVER['SERVER_ADDR'] is not set, it gets the value from $_SERVER['LOCAL_ADDR'], needed on some Windows servers.
 */
if (!$_SERVER['SERVER_ADDR']) $_SERVER['SERVER_ADDR'] = $_SERVER['LOCAL_ADDR'];
/**
 * In case $_SERVER['REMOTE_ADDR'] is not set, it gets the value from $_SERVER['REMOTE_HOST'], needed on some Windows servers.
 */
if (!$_SERVER['REMOTE_ADDR']) $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_HOST'];

/**
 * @global bool $c_jp7
 */
$c_jp7 = ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['REMOTE_ADDR'] == '201.6.156.39' || strpos($_SERVER['REMOTE_ADDR'], '192.168.0') === 0 || $_SERVER['SERVER_ADDR'] == '192.168.0.2');

/**
 * Setting "setlocale", "allow_url_fopen" and "error_reporting". And calling jp7_register_globals().
 */
if (!setlocale(LC_CTYPE, 'pt_BR')) setlocale(LC_CTYPE, 'pt_BR.ISO8859-1');
if ($c_jp7) error_reporting(E_ALL ^ E_NOTICE);
else error_reporting(0);
if (!@ini_get('allow_url_fopen')) @ini_set('allow_url_fopen', '1');
jp7_register_globals();

/**
 * @global Jp7_Debugger $debugger
 */
$debugger = new Jp7_Debugger();
//set_error_handler(array($debugger, 'errorHandler'));

/**
 * @global Browser $is
 */
$is = new Browser($_SERVER['HTTP_USER_AGENT']);

/**
 * Define o diretÛrio com os arquivos do Krumo
 */
define('KRUMO_DIR', dirname(__FILE__) . '/../_default/js/krumo/');

/**
 * Includes a class in case it hasn't been defined yet.
 *
 * @param string $className Name of the class
 * @return void
 * @global Jp7_Debugger
 */
function __autoload($className){
	global $debugger;
	
	if (!$className) return;
	
	$classNameArr = explode('_', $className);
	$filename = implode('/', $classNameArr) . (($classNameArr[0] == 'Zend') ? '' : '.class') . '.php';
	
	$paths = explode(PATH_SEPARATOR, get_include_path());
	$paths[] = '../classes';
	
	$i = 0;
	$file = '';
	while (!@file_exists($file)) {
		if (isset($paths[$i])) { 
			$file = jp7_path_find($paths[$i] . '/' . $filename, true);
		} else {
			if ($debugger) $debugger->addLog('autoload() could not find the (' . $className . ') class.', 'error');
			return;
		}
		$i++;
	}
	require_once($file);
}

/**
 * Takes off diacritics and empty spaces from a string, if $tofile is <tt>FALSE</tt> (default) the case is changed to lowercase.
 *
 * @param string $S String to be formatted.
 * @param bool $tofile Sets whether it will be used for a filename or not, <tt>FALSE</tt> is the default value.
 * @param string $separador	Separator used to replace empty spaces.
 * @return string Formatted string.
 * @version (2006/01/18)
 */
function toId($S,$tofile = FALSE, $separador=''){
	if ($separador) $S = str_replace(' ', $separador, $S);
	$S = preg_replace("([·‡„‚‰¡¿√¬ƒ™])", 'a', $S);
	$S = preg_replace("([ÈËÍÎ…» À&])", 'e', $S);
	$S = preg_replace("([ÌÏÓÔÕÃŒœ])", 'i', $S);
	$S = preg_replace("([ÛÚıÙˆ”“’‘÷∫])", 'o', $S);
	$S = preg_replace("([˙˘˚¸⁄Ÿ€‹])", 'u', $S);
	$S = preg_replace("([Á«])", 'c', $S);
	$S = preg_replace("([Ò—])", 'n', $S);
	if($tofile){
		$S = preg_replace("([^(\d\w)])", '_', $S);
	}else{
		$S = preg_replace("([^(\d\w)])", $separador, $S);
		$S = strtolower($S);
	}
	$S = preg_replace("([\(\)])", '', $S);
	if ($separador != '-') $S = preg_replace("([/-])", '_', $S);
	return $S;
}

/**
 * Takes off diacritics from a string and replace special characters and empty spaces by '-'. 
 *
 * @param string $S String to be formatted.
 * @return string Formatted string.
 * @author JP
 * @version (2008/06/12) update by Carlos Rodrigues
 */
function toSeo($S) {
	$S = preg_replace("([·‡„‚‰¡¿√¬ƒ™])", 'a', $S);
	$S = preg_replace("([ÈËÍÎ…» À&])", 'e', $S);
	$S = preg_replace("([ÌÏÓÔÕÃŒœ])", 'i', $S);
	$S = preg_replace("([ÛÚıÙˆ”“’‘÷∫])", 'o', $S);
	$S = preg_replace("([˙˘˚¸⁄Ÿ€‹])", 'u', $S);
	$S = preg_replace("([Á«])", 'c', $S);
	$S = preg_replace("([Ò—])", 'n', $S);
	$S = preg_replace("([^\d\w- /])", '', $S);
	$S = preg_replace("([ -/]+)", '-', trim($S));
	return strtolower($S);
}

/**
 * Generates a SQL WHERE statement with REGEXP for 'decoding' the toSeo() function.
 *
 * @param string $field Field where the data will be searched, e.g. varchar_key.
 * @param string $str String to be formatted and searched.
 * @param string $regexp Optional REGEXP string, the default value is '[^\d\w]?'.
 * @return string Formatted SQL WHERE statement with a REGEXP.
 * @author Carlos Rodrigues
 * @version (2008/06/12) 
 */
function toSeoSearch($field, $str, $regexp = '[^[:alnum:]]*'){
	$sql_where = $regexp;
	for ($i = 0; $i < strlen($str); $i++){
		$char = $str[$i];
		$char = str_replace('a', '[a·‡„‚‰™]', $char);
		$char =	str_replace('e', '[eÈËÍÎ&]', $char);
		$char = str_replace('i', '[iÌÏÓÔ]', $char);
		$char =	str_replace('o', '[oÛÚıÙˆ∫]', $char);
		$char =	str_replace('u', '[u˙˘˚¸]', $char);
		$char =	str_replace('c', '[cÁ]', $char);
		$char =	str_replace('n', '[nÒ]', $char);
		$sql_where .= $char . $regexp;
	}
	return "REPLACE(".$field.",' ','') REGEXP '^" . $sql_where . "$'";
}

/**
 * Takes off diacritics from a string and replaces linebreaks by <br/>.
 *
 * @param string $S The input string.
 * @global bool 
 * @return string Formatted string.
 * @todo It still needs to be documented the usage of global $html.
 * @version (2005/08/10)
 */
function wap_toHTML($S){
	global $html;
	if(!$html)$S = str_replace("$","$$",$S);
	$S=str_replace(chr(13),"<br/>",$S);
	$S=str_replace("<br>","<br/>",$S);
	$S=preg_replace("([·‡„‚‰™])","a",$S);
	$S=preg_replace("([ÈËÍÎ&])","e",$S);
	$S=preg_replace("([ÌÏÓÔ])","i",$S);
	$S=preg_replace("([ÛÚıÙˆ∫])","o",$S);
	$S=preg_replace("([˙˘˚¸])","u",$S);
	$S=preg_replace("([Á])","c",$S);
	$S=preg_replace("([Ò])","n",$S);
	$S=preg_replace("([¡¿√¬ƒ])","A",$S);
	$S=preg_replace("([…» À&])","E",$S);
	$S=preg_replace("([ÕÃŒœ])","I",$S);
	$S=preg_replace("([”“’‘÷])","O",$S);
	$S=preg_replace("([⁄Ÿ€‹])","U",$S);
	$S=preg_replace("([«])","C",$S);
	$S=preg_replace("([—])","N",$S);
	return $S;
}

/**
 * Quotes a string to be sent to the database. e.g. mysql becomes 'mysql'.
 *
 * @param string $S The input string.
 * @param bool $force_magic_quotes_gpc If TRUE the string will be quoted even if 'magic_quotes_gpc' is not active.
 * @global ADOConnection
 * @return string Quoted string.
 * @version (2003/08/25)
 */
function toBase($S,$force_magic_quotes_gpc=FALSE){
	global $db;
	if($S){
		$S=$db->qstr($S,get_magic_quotes_gpc()&&!$force_magic_quotes_gpc); //trata as aspas. Ex.: 'mysql' fica \'mysql\'
		$S=trim($S);
	}else{
		$S="''";
	}
	return $S;
}

/**
 * Replaces double and single quotes so they can be used inside an HTML element's attribute. e.g. \'test\' becomes &#39;test&#39;
 *
 * @param string $S String to be formatted.
 * @return string Formatted string.
 * @version (2004/06/14)
 */
function toForm($S){
	$S=str_replace("\'","&#39;",$S);// Bug LocaWeb e JavaScript
	$S=str_replace('\"','"',$S);// Bug LocaWeb
	return stripslashes(str_replace("\"","&quot;",$S));
}

/**
 * Formats an string to be used as HTML text, strips slashes and replaces values.
 *
 * @param string $S String to be formatted.
 * @param bool $HTML If <tt>FALSE</tt> (default) the line breaks are replaced by <br />
 * @param bool $busca_replace If <tt>TRUE</tt> the function uses the regex string ($busca_varchar or $busca_text, passed by globals) to replace values. <tt>FALSE</tt> is the default value.
 * @global string
 * @global string
 * @return string Formatted string.
 * @version (2004/06/14)
 */
function toHTML($S,$HTML=FALSE,$busca_replace=FALSE){
	global $busca_varchar, $busca_text;
	$busca=($busca_varchar)?$busca_varchar:$busca_text;
	if($S){
		if(!$HTML)$S=str_replace(chr(13)," <br /> ",$S);
		//elseif(strpos(strtolower($S),"<p>")===false)$S="<p>".$S."</p>";
		$S=str_replace("\'","'",$S);// Bug LocaWeb
		$S=str_replace("''","'",$S);// Bug LocaWeb
		$S=str_replace('\"','"',$S);// Bug LocaWeb
		if($busca_replace&&$busca)$S=preg_replace("/[^@\.]".$busca."[^@\.]/i"," <span class=\"font-search\">".strtoupper($busca)."</span> ",$S);
		return stripslashes($S);
	}
}

/**
 * Formats a string to be used inside a javascript. Replaces \" by &quot; and ' by \'.
 *
 * @param string $S String to be formatted.
 * @return string Formatted string.
 * @version (2004/05/31)
 */
function toScript($S){
	$S=str_replace("\r",'\r',$S);
	$S=str_replace("\n",'\n',$S);
	$S=str_replace("\"","&quot;",$S);
	$S=str_replace("'","\'",$S);
	return $S;
}

/**
 * Formats a string to be used inside a parameter. Replaces \" by &quot; and line breaks by empty spaces (" ").
 *
 * @param string $S String to be formatted.
 * @return string Formatted string.
 * @version (2007/06/25)
 * @author JP
 */
function toParam($S){
	$S=str_replace("\"","&quot;",$S);
	$S=str_replace("\n"," ",$S);
	$S=str_replace("\r"," ",$S);
	$S=str_replace(chr(13)," ",$S);
	$S=str_replace(chr(11)," ",$S);
	return $S;
}

/**
 * Formats a string to be used inside a XML.
 *
 * @param string $S String to be formatted.
 * @return string Formatted string.
 * @version (2008/12/05)
 * @author JP7
 */
function toXml($S) {
	return str_replace(array('&', '"', "'", '<', '>', 'í' ), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;', '&apos;'), $S);
}

/**
 * Converts Hex string into binary string.
 *
 * @param string $S String to be converted.
 * @return string Binary string.
 * @version (2007/01/22)
 * @author JP
 */
function hex2bin($S){
	return pack("H".strlen($S),$S); 
}

/**
 * Encrypts a string using a key.
 *
 * @param string $S String that will be encrypted.
 * @param string $key Key with which the data will be encrypted, the key will be required to decrypt it as well, the default value is the md5 hash of $_SERVER["HTTP_HOST"].
 * @param string $cipher One of the MCRYPT_ciphername constants of the name of the algorithm, the default value is <tt>MCRYPT_RIJNDAEL_128</tt>.
 * @param string $mode One of the MCRYPT_MODE_modename constants, the default value is <tt>MCRYPT_MODE_ECB</tt>.
 * @return string Encrypted string.
 * @version (2007/04/19)
 * @author JP
 */
function jp7_encrypt($S,$key="",$cipher=MCRYPT_RIJNDAEL_128,$mode=MCRYPT_MODE_ECB){
	if(!$key)$key=md5($_SERVER["HTTP_HOST"]);
	$iv=mcrypt_create_iv(mcrypt_get_iv_size($cipher,$mode),MCRYPT_RAND);
	return bin2hex(mcrypt_encrypt($cipher,$key,$S,$mode,$iv));
}

/**
 * Decrypts a string using a key.
 *
 * @param string $S Encrypted string.
 * @param string $key Key with which the data was encrypted, the default value is the md5 hash of $_SERVER["HTTP_HOST"].
 * @param string $cipher One of the MCRYPT_ciphername constants of the name of the algorithm, the default value is <tt>MCRYPT_RIJNDAEL_128</tt>.
 * @param string $mode One of the MCRYPT_MODE_modename constants, the default value is <tt>MCRYPT_MODE_ECB</tt>.
 * @return string Decrypted string.
 * @version (2007/04/19)
 * @author JP
 */
function jp7_decrypt($S,$key="",$cipher=MCRYPT_RIJNDAEL_128,$mode=MCRYPT_MODE_ECB){
	if(!$key)$key=md5($_SERVER["HTTP_HOST"]);
	$iv=mcrypt_create_iv(mcrypt_get_iv_size($cipher,$mode),MCRYPT_RAND);
	return trim(mcrypt_decrypt($cipher,$key,hex2bin($S),$mode,$iv),"\0");
}

/**
 * Changes the case of common HTML tags to lowercase, changes the align attribute on <p>, and close <br> tags, adapting it to XHTML standards. The affected tags are: <P>, <BR>, <IMG>, <TABLE>, <TR>, <TH> and <TD>.
 *
 * @param string $S HTML string.
 * @return string XHTML string.
 * @version (2005/10/19)
 */
function toXHTML($S){
	$S=str_replace("<P>","<p>",$S);
	$S=str_replace("<P ","<p ",$S);
	$S=str_replace("</P>","</p>",$S);
	$S=str_replace("<BR>","<br />",$S);
	$S=str_replace("<IMG ","<img ",$S);
	$S=str_replace("<TABLE","<table",$S);
	$S=str_replace("<TR","<tr",$S);
	$S=str_replace("<TH","<th",$S);
	$S=str_replace("<TD","<td",$S);
	$S=str_replace("</TABLE","</table",$S);
	$S=str_replace("</TR","</tr",$S);
	$S=str_replace("</TH","</th",$S);
	$S=str_replace("</TD","</td",$S);
	$S=str_replace("<p align=left>","<p>",$S);
	$S=str_replace("<p align=justify>","<p>",$S);
	$S=str_replace("<p align=center>","<p style=\"text-align:center\">",$S);
	$S=str_replace("<p align=right>","<p style=\"text-align:right\">",$S);
	return $S;
}

/**
 * Checks if the referer page is the same as it was expected to be.
 *
 * @param string $S Expected referer page's URL.
 * @param string $protocol Protocol used, the default value is "http".
 * @return bool <tt>TRUE</tt> if the referer is the expected page, <tt>FALSE</tt> if not.
 * @version (2008/05/19)
 */
function checkReferer($S, $protocol="http"){
	/*
	while(strpos($S,"../")!==false){
	}
	*/
	if(!dirname($S)||dirname($S)=="."){
		$S_parent = $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['REQUEST_URI'])) . '/' . $S;
		$S = $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $S;
	}
	return (strpos($_SERVER['HTTP_REFERER'], $S) === 0 || strpos($_SERVER['HTTP_REFERER'], $S_parent) === 0);
}

/**
 * Shrinks the input string and adds "..." if it is larger than the maximum length, the input string is not changed if its shorter.
 *
 * @param string $S Input string.
 * @param int $length Max. lenght of the output string.
 * @return string Shrunk string.
 * @version (2008/07/04)
 * @global string
 * @global string
 */
function jp7_string_left($S, $length){
	global $s_session, $c_lang;
	if ($c_lang){
		foreach($c_lang as $item){
			if ($item[0] == $s_session['lang'] && $item[2]) $length = $length * 8; // Check if language uses entities for characters (eg.: japanese)
		}
	}
	return (strlen($S) > $length) ? substr($S, 0, $length) . "..." : $S;
}

/**
 * Sets global variables using values from superglobals if "register_globals" is OFF, emulating this feature.
 *
 * @global string
 * @todo Check if this function could be flagged as "deprecated".
 * @version (2007/03/03)
 */
function jp7_register_globals(){
	global $HTTP_HOST;
	if(!@ini_get('register_globals') || !$HTTP_HOST){
		if ($_SERVER) foreach ($_SERVER as $key=>$value){ $GLOBALS[$key] = $_SERVER[$key]; }
		if ($_GET) foreach ($_GET as $key=>$value){ $GLOBALS[$key] = $_GET[$key]; }
		if ($_POST) foreach ($_POST as $key=>$value){ $GLOBALS[$key] = $_POST[$key]; }
		if ($_COOKIE) foreach ($_COOKIE as $key=>$value){ $GLOBALS[$key] = $_COOKIE[$key]; }
		if ($_SESSION) foreach ($_SESSION as $key=>$value){ $GLOBALS[$key] = $_SESSION[$key]; }
	}
}

/**
 * Creates an alphanumeric password (a-z, 0-9).
 *
 * @param string $length Length of the created password, the default value is 6.
 * @return string Created password.
 * @version (2008/09/25)
 * @author JP
 */
function jp7_password($length = 6) {
	$chars = 'abcdefghijkmnopqrstuvwxyz023456789';
	$S = '';
	for($i = 0; $i < $length; $i++) {
		$S .= substr($chars, rand(0, strlen($chars) - 1), 1);
	}
	return $S;
}

/**
 * Formats and prints the elements of an array or object, using the print_r() function and adding the "pre" tag around it.
 *
 * @param mixed $var Array or object that will have its elements printed.
 * @param bool $return If <tt>TRUE</tt> the formatted string is returned, otherwise its printed, default value is <tt>FALSE</tt>.
 * @param bool $hideProtectedVars If <tt>TRUE</tt> the print_r will not show protected properties of an object. This feature is not recursive.
 * @param string @varPrefix If <tt>TRUE</tt> it will only print the keys starting by this prefix. Is is useful when printing large arrays, like $GLOBALS.
 * @return string|NULL Formatted string or <tt>NULL</tt>. 
 * @version (2008/02/06)
 * @author JP
 */
function jp7_print_r($var, $return = FALSE, $hideProtectedVars = FALSE, $varPrefix = '') {
	
	if ($hideProtectedVars) {
		if (is_object($var)) {
			$array[0] = (array) $var;
		} elseif (is_array($var) && is_object(reset($var))) {
			foreach ($var as $key => $value) {
				$array[$key] = (array) $value;
			} 
		}
	} elseif ($varPrefix && is_array($var)) {
		$array = $var;
	}
		
	if ($array) {
		foreach ($array as $key => $value) {
			if ($varPrefix && strpos($key, $varPrefix) !== 0)  continue;
			if ($hideProtectedVars) {
				foreach ($value as $valueKey => $valueValue) {
					if (strpos($valueKey, chr(0) . chr(42) . chr(0)) === 0) {
						$array[$key][substr($valueKey, 2) . ':protected'] = '*PROTECTED*'; 
						unset($array[$key][$valueKey]); // Retira os valores protected
					}
				}
			}
		}
		if (is_object($var) && $hideProtectedVars) $array = $array[0];
		$S = print_r($array, TRUE);
	} else {
		$S = print_r($var, TRUE);
	}
	
	$S = "<pre style=\"text-align:left\">" . $S . "</pre>";
		
	if ($return) return $S;
	else echo $S;
}

/**
 * Splits a time/date into an array.
 *
 * @param string $date String containing a date/time on the format Y-m-d H:i:s or Y/m/d H:i:s.
 * @return array Array containing the following keys: Y, m, M, d, H, i, s and y.
 * @version (2008/05/27)
 */
function jp7_date_split($date){
	$date=str_replace(" ",",",$date);
	$date=str_replace("/",",",$date);
	$date=str_replace("-",",",$date);
	$date=str_replace(":",",",$date);
	$date=split(",",$date);
	return array(
		Y=>$date[0],
		m=>$date[1],
		M=>jp7_date_month($date[1],true),
		F=>jp7_date_month($date[1]),
		d=>$date[2],
		H=>$date[3],
		i=>$date[4],
		s=>$date[5],
		y=>substr($date[0],2)
	);
}

/**
 * Returns date formatted according to given format.
 *
 * @param string $date Date/time string.
 * @param string $format Format using: "Y", "m", "M", "d", "H", "i", "s" or "y". The default value is "d/m/Y", when english language is active the "d/m" is automatically replaced by "m/d". 
 * @global string
 * @return string|NULL Returns formatted date or <tt>NULL</tt> if no date is given.
 * @version (2006/08/24)
 */
function jp7_date_format($date,$format="d/m/Y"){
	global $lang;
	if($date){
		if($lang->lang=="en"){
			$format=str_replace("d/m","m/d",$format);
			$format=str_replace("d-m","m-d",$format);
		}
		$date=jp7_date_split($date);
		$S="";
		for($i = 0;$i<strlen($format);$i++){
			$x=substr($format,$i,1); 
			$S.=($date[$x])?$date[$x]:$x;
		}
		return $S;
	}
}

/**
 * Returns textual representation for the day of the week, such as Sunday or Saturday. Supports english and portuguese.
 *
 * @param int|string $w A numeric representation of the day of the week (0 for Sunday through 6 for Saturday), or a date/time string.
 * @param string $sigla If <tt>TRUE</tt> returns only the first three letters, the default value is <tt>FALSE</tt>.
 * @global string
 * @return string Textual representation for the day of the week.
 * @version (2006/04/27)
 */
function jp7_date_week($w, $sigla = FALSE) {
	global $lang;
	switch($lang->lang) {
		case "en": $W = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"); break;
		case "de": $W = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag"); break;
		case "es": $W = array("Domingo", "Lunes", "Martes", "MiÈrcoles", "Jueves", "Viernes", "S·bado"); break;
		default: $W = array("Domingo", "Segunda", "TerÁa", "Quarta", "Quinta", "Sexta", "S·bado"); break;
	}
	if (!is_int($w)) $w = date("w", strtotime($w));
	$return = $W[$w];
	return ($sigla) ? substr($return, 0, 3) : $return;
}

/**
 * Returns textual representation of a month, such as January or March. Supports english and portuguese.
 *
 * @param int $m Numeric representation of a month, (1 for January through 12 for December).
 * @param string $sigla If <tt>TRUE</tt> returns only the first three letters, the default value is <tt>FALSE</tt>.
 * @global string
 * @return string Textual representation of a month.
 * @version (2004/06/14)
 */
function jp7_date_month($m, $sigla = FALSE) {
	global $lang;
	switch($lang->lang) {
		case "en": $M = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"); break;
		case "de": $M = array("Januar", "Februar", "M‰rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"); break;
		case "es": $M = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"); break;
		default: $M = array("Janeiro", "Fevereiro", "MarÁo", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"); break;
	}
	$return = $M[$m - 1];
	return ($sigla) ? substr($return, 0, 3) : $return;
}

/**
 * Calculates the number of months from the start date to the end date.
 *
 * @param string $start Start date, string on the "Y-m-d" format.
 * @param string $end End date, string on the "Y-m-d" format. 
 * @return int Number of months.
 * @author Paulo
 * @todo Add functionality to return days and years too, and to take off the time aggregated with a date, like: 2008-10-08 00:01:02.
 * @version (2008/04/15)
 */
function jp7_date_diff($start,$end){
	$start = explode("-",$start);
	$start = mktime(0,0,0,$start[1],$start[2],$start[0]); // mes / dia / ano (padrao mktime)
	$end   = explode("-",$end); 
	$end   = mktime(0,0,0,$end[1],$end[2],$end[0]); // mes / dia / ano (padrao mktime)
	$diff  = ($end - $start);
	$diff  = explode("-",date("Y-m-d",$diff));
	$diff_r['m']= $diff[1];
	return $diff_r['m'];
}

/**
 * Splits a telephone number into "ddd", "numero" and "ramal".
 *
 * @param string $tel String containing a telephone number.
 * @return array Array containing "ddd", "numero" and "ramal".
 * @todo Add support for poorly formatted telephones like: "-Ramal:", " R:", " - R:", maybe taking off empty spaces and "-".
 * @version (2004/08/12)
 */
function jp7_tel_split($tel){
	$tel=str_replace("(","",$tel);
	$tel=str_replace(")",",",$tel);
	$tel=str_replace(" - Ramal: ",",",$tel);
	$tel=split(",",$tel);
	return array(
		ddd=>trim($tel[0]),
		numero=>trim($tel[1]),
		ramal=>trim($tel[2])
	);
}

/**
 * Searches for a value on the database and creates global variables from the result.
 *
 * @param string $table Name of the table where it will search.
 * @param string $table_id_name Name of the key field.
 * @param mixed $table_id_value Value expected for the key field.
 * @param string $var_prefix Prefix used when creating the global variables from the result, on the format: prefix + field name, the default value is "".
 * @global ADOConnection
 * @global bool
 * @return NULL Nothing is returned, but the function creates global variables.
 * @version (2006/08/23)
 */
function jp7_db_select($table,$table_id_name,$table_id_value,$var_prefix=""){
	global $db, $jp7_app;
	$sql = "SELECT * FROM ".$table." WHERE ".$table_id_name."=".$table_id_value;
	$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
	while ($row = $rs->FetchNextObj()) {
		$meta_cols=$db->MetaColumns($table, FALSE);
		foreach ($meta_cols as $meta){
			$name=$meta->name;
			// Dates
			if(strpos($meta->type,"date")!==FALSE){
				$GLOBALS[$var_prefix.$name]=$row->$name;
				$GLOBALS[$var_prefix.$name."_split"]=jp7_date_split($row->$name);
				$GLOBALS[$var_prefix.$name."_time"]=strtotime($row->$name);
			}else{
				if($jp7_app)$GLOBALS[$var_prefix.$name]=toForm($row->$name);
				else $GLOBALS[$var_prefix.$name]=$row->$name;
			}	
		}
	}
	$rs->Close();
}

/**
 * Inserts or updates a record on the given table using values from global variables. 
 *
 * @param string $table Name of the table where it will insert or update data.
 * @param string $table_id_name Name of the key field.
 * @param mixed $table_id_value Value expected for the key field, the default value is 0. If a value is given the row is updated, otherwise it is inserted. 
 * @param mixed $var_prefix Prefix used to get values from global variables, the default value is "". e.g. For the field name "varchar_1" and the global variable "pre_varchar_1", the prefix should be "pre_". If it is passed as an array, the values from this array will be used instead of globals.
 * @param bool $var_check If <tt>FALSE</tt> prepares the data for empty and null values before updating, the default value is <tt>TRUE</tt>.
 * @global ADOConnection
 * @return int When updating: $table_id_value on success or 0 on error. When inserting: the inserted record¥s ID.
 * @author JP, Cristiano
 * @version (2007/12/17)
 */
function jp7_db_insert($table, $table_id_name, $table_id_value = 0, $var_prefix = "", $var_check = TRUE, $force_magic_quotes_gpc = FALSE) {
	global $db;
	
	$table_columns=$db->MetaColumnNames($table);
	array_shift($table_columns); // ID is the first value
	$table_columns_num=count($table_columns);
	if($table_id_value){
		// Update
		$sql = "UPDATE ".$table." SET ";
		$j=0;
		foreach($table_columns as $table_field_name){
			if (is_array($var_prefix)) {
				$var_isset = array_key_exists($table_field_name, $var_prefix);
				$table_field_value = $var_prefix[$table_field_name];
			} else {
				eval("global \$".$var_prefix.$table_field_name.";");
				eval("\$var_isset=isset(\$".$var_prefix.$table_field_name.");");
				eval("\$table_field_value=\$".$var_prefix.$table_field_name.";");
			}
			if(!$var_check||$var_isset){
				//se for definido valor ou campo for inteiro
				if(($table_field_value!=="" && !is_null($table_field_value))||strpos($table_field_name,"int_")===0){
					$sql.=((!$j)?" ":",")."".$table_field_name."=".toBase($table_field_value,$force_magic_quotes_gpc);
				//se n„o for definido valor e for mysql salva branco
				}elseif(($table_field_value==="" || is_null($table_field_value)) && ($GLOBALS['db_type']==""||$GLOBALS['db_type']=="mysql")){
					$sql.=((!$j)?" ":",")."".$table_field_name."=''";
				//se n„o for definido valor e for != de mysql
				}else{
					$sql.=((!$j)?" ":",")."".$table_field_name."=NULL";
				}
				$j++;
			}
		}
		$sql.=" WHERE ".$table_id_name."=".$table_id_value;
		$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
		return ($rs)?$table_id_value:0;
	}else{		
		// Insert
		$i=1;
		foreach($table_columns as $table_field_name){
			if (is_array($var_prefix)) {
				$table_field_value = $var_prefix[$table_field_name];
			} else {
				eval("global \$".$var_prefix.$table_field_name.";");
				eval("\$table_field_value=\$".$var_prefix.$table_field_name.";");
			}
			$sql_campos.=" ".$table_field_name." ".(($i==$table_columns_num)?") ":",\n");
			//se for definido valor
			if(($table_field_value!=="" && !is_null($table_field_value))||strpos($table_field_name,"int_")===0){
				$valores.=toBase($table_field_value,$force_magic_quotes_gpc).(($i==$table_columns_num)?")":",\n");
			//se n„o for definido valor e for mysql salva branco
			}elseif(($table_field_value==="" || is_null($table_field_value)) && ($GLOBALS['db_type']==""||$GLOBALS['db_type']=="mysql")){
				$valores.="''".(($i==$table_columns_num)?")":",\n");
			//se n„o for definido valor e for != de mysql
			}else{
				$valores.="NULL".(($i==$table_columns_num)?")":",\n");
			}
			$i++;
		}
		$sql = "INSERT INTO ".$table." (".$sql_campos."VALUES (".$valores;//echo $sql ."<br /><hr /><br />";
		$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(), $sql));
		// Last ID
		eval("global \$".$var_prefix.$table_id_name.";");
		eval("\$".$var_prefix.$table_id_name."=".$db->Insert_ID().";");
		return $db->Insert_ID();
	}
}

/**
 * class jp7_db_pages
 *
 * @version (2007/02/22)
 * @package 7lib
 * @subpackage jp7_db_pages
 * @deprecated Kept as an alias to Pagination class.
 */
class jp7_db_pages extends Pagination{
	// Alterado o nome para Pagination
}

/**
 * Creates a checkbox and a hidden field, the hidden field will have a value or not depending on whether the checkbox is checked or not.
 *
 * @param string $name Name of the hidden field.
 * @param string $value Value that the hidden field will have if the checkbox is checked, the default value is "S".
 * @param string $var Name of global variable containing the current value for the hidden field, the default value is "".
 * @param string $readonly Readonly parameter to be inserted on the checkbox. e.g. readonly="readonly"
 * @param string $xtra Additional HTML parameter to be inserted on the checkbox.
 * @return string If $GLOBALS["interadmin_visualizar"] is set it returns "Sim" or "N„o", otherwise it returns the created HTML for checkbox and hidden field.
 * @todo Make $readonly a boolean, setting if the field is readonly or not. Check if its better to replace $GLOBALS["interadmin_visualizar"] by global $interadmin_visualizar.
 * @author JP
 * @version (2007/07/13)
 */
function jp7_db_checkbox($name,$value="S",$var="",$readonly="",$xtra=""){
	if(!$var)$var=$name;
	$var_value=$GLOBALS[$var];
	if($GLOBALS["interadmin_visualizar"]){
		return (($var_value)?"Sim":"N„o");
	}else{
		return "".
		"<input type=\"checkbox\" name=\"jp7_db_checkbox_".$name."\" id=\"jp7_db_checkbox_".$name."\" value=\"".$value."\"".(($var_value)?" checked=\"checked\"":"").$readonly." onclick=\"form['".$name."'].value=(checked)?value:''\"".(($xtra)?" ".$xtra:"")." />".
		"<input type=\"hidden\" name=\"".$name."\" value=\"".(($var_value)?$value:"")."\" />";
	}
}

/**
 * Updates a record on the given table using values from global variables. 
 *
 * @param string $table Name of the table where it will update data.
 * @param string $table_id_name Name of the key field.
 * @param mixed $table_id_value Value expected for the key field. 
 * @param string $fields Names of the fields that will be updated separated by comma (,). e.g. 'name1,name2,name3'.
 * @global ADOConnection
 * @return NULL Nothing is returned.
 * @author JP
 * @version (2006/04/18)
 */
function jp7_db_update($table,$table_id_name,$table_id_value,$fields){
	global $db;
	$fields_arr=split(",",$fields);
	// Vari·veis
	foreach($fields_arr as $field){
		$fields_arr_db[]=(strpos($field,"_")===0)?substr($field,1):$field;
	}
	foreach($fields_arr_db as $field_db){
		eval("global \$".$field_db.";");
	}
	// Update Concatenado (_)
	$sql = "SELECT ".implode(",",$fields_arr_db)." FROM ".$table." WHERE ".$table_id_name."=".$table_id_value;
	$rs = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(),$sql));
	if ($row =(array)$rs->FetchNextObj()){
		foreach($fields_arr as $field){
			if(strpos($field,"_")===0){
				$field=substr($field,1);
				eval("\$".$field.".=\"".$row[$field]."\";");
			}
		}
	}
	$rs->Close();
	// Update
	$sql = "UPDATE ".$table." SET ";
	for($i = 0;$i<count($fields_arr_db);$i++){
		eval("\$field_value=\$".$fields_arr_db[$i].";");
		$sql.=$fields_arr_db[$i]."='".$field_value."'";
		if($i!=count($fields_arr_db)-1)$sql.=",";
	}
	$sql.=" WHERE ".$table_id_name."=".$table_id_value;
	$rs = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(),$sql));
}

/**
 * Creates an array from a given list of fields using Interadmin's format.
 *
 * @param string $campos String containing the fields of a type, fields separated by {;}, parameters separated by {,}.
 * @return array Array of fields with its parameters.
 * @author JP
 * @version (2007/03/10)
 */
function interadmin_tipos_campos($campos){
	$campos_parameters=array("tipo","nome","ajuda","tamanho","obrigatorio","separador","xtra","lista","orderby","combo","readonly","form","label","permissoes","default","nome_id");
	$campos=split("{;}",$campos);
	for($i = 0;$i<count($campos);$i++){
		$parameters=split("{,}",$campos[$i]);
		if($parameters[0]){
			$A[$parameters[0]][ordem]=($i+1);
			for($j=0;$j<count($parameters);$j++){
				$A[$parameters[0]][$campos_parameters[$j]]=$parameters[$j];
			}
		}
	}
	return $A;
}

/**
 * Gets an array containing "nome" and "xtra" values of a field on Interadmin. 
 *
 * @param string $db_prefix Prefix of the table.
 * @param string $id_tipo ID of the type which will be searched (column "id_tipo").
 * @param string $var_key Name of the field from this type that will be got. e.g. "varchar_key".
 * @global ADOConnection
 * @global string
 * @global int
 * @return array Array containing "nome" and "xtra" values of the field.
 * @version (2004/11/03)
 */
function interadmin_tipos_campo($db_prefix,$id_tipo,$var_key){
	global $db, $tipo_campos, $tipo_model_id_tipo;
	$tipo_model_id_tipo=$id_tipo;
	while($tipo_model_id_tipo){
		jp7_db_select($db_prefix."_tipos","id_tipo",$tipo_model_id_tipo,"tipo_");
	}
	$tipo_campos=split("{;}",$tipo_campos);
	foreach($tipo_campos as $campo){
		$campo=split("{,}",$campo);
		if($campo[0]==$var_key){
			return array(
				nome=>$campo[1],
				xtra=>$campo[6]
			);
			break;
		}
	}
}

/**
 * Alias for interadmin_query().
 *
 * @deprecated
 * @see interadmin_query()
 * @author JP
 * @version (2007/04/25)
 */
function interadmin_mysql_query($sql,$sql_db="",$sql_debug=false){
	return interadmin_query($sql,$sql_db,$sql_debug);
}

/**
 * Runs a SQL query and returns its recordset.
 *
 * @param string $sql SQL query which will be executed.
 * @param ADOConnection $sql_db Database which will be used, the default value is "".
 * @param bool $sql_debug Formats and prints the SQL string for debug purposes, the default value is <tt>FALSE</tt>.
 * @param int $numrows Number of records to be retrieved from the database, the default value is <tt>NULL</tt>.
 * @param int $offset Number of ignored records before is starts retrieving, the default value is <tt>NULL</tt>.
 * @return ADORecordSet Recordset object.
 * @todo Check if 'if($rs&&$sql)eval("global \$".$rs.";\$".$rs."=\$rs_pre;");' is needed.
 * @author JP
 * @version (2007/03/04)
 */
function interadmin_query($sql, $sql_db = "", $sql_debug = FALSE, $numrows = NULL, $offset = NULL){	
	global $c_publish;
	global $c_path_upload;
	global $s_session;
	global $db;
	global $db_prefix;
	global $lang;
	global $debugger;
	
	$DbNow = $db->BindTimeStamp(date("Y-m-d H:i:s"));
	
	// Debug - Before SQL injection
	$debugger->showSql($sql, $sql_debug, 'color:#FFFFFF;background:#444444;');
	
	// Split
	$sql_slipt = preg_replace(array('/([	 ])(FROM )/','/([	 ])(WHERE )/','/([ 	])(ORDER BY )/'), '{;}\1\2', $sql, 1);
	$sql_slipt = explode("{;}", $sql_slipt);
	foreach ($sql_slipt as $value) {
		if(!$sql_select && strpos($value, "SELECT ") !== FALSE) $sql_select = $value;
		if(!$sql_from && strpos($value, "FROM ") !== FALSE) $sql_from = $value;
		if(!$sql_where && strpos($value, "WHERE ") !== FALSE) $sql_where = $value;
		if(!$sql_final && strpos($value, "ORDER BY ") !== FALSE) $sql_final = $value;
	}
	// Parser
	preg_match_all("(([^ ,]+) AS ([^ ,]+))", $sql_from, $out, PREG_PATTERN_ORDER);
	if (count($out[1])) {
		// Com Alias
		foreach ($out[1] as $key=>$value) {
			$alias = $out[2][$key];
			if (strpos($value, $db_prefix . '_tipos') !== false) {
				$sql_where = str_replace("WHERE ","WHERE (" . $alias . ".mostrar<>'' OR " . $alias . ".mostrar IS NULL) AND (" . $alias . ".deleted_tipo='' OR " . $alias . ".deleted_tipo IS NULL) AND ", $sql_where);
			} elseif (strpos($value, $db_prefix . $lang->prefix . '_arquivos')!==false || strpos($value, $db_prefix . '_arquivos') !== false) {
				$sql_where = str_replace("WHERE ","WHERE " . $alias . ".mostrar<>'' AND (" . $alias . ".deleted='' OR " . $alias . ".deleted IS NULL) AND ", $sql_where);
			} else {
				$sql_where_replace = '' .
				"WHERE (" . $alias . ".date_publish<='" . $DbNow . "' OR " . $alias . ".date_publish IS NULL)" .
				" AND (" . $alias . ".date_expire>'" . $DbNow . "' OR " . $alias . ".date_expire IS NULL OR " . $alias . ".date_expire='0000-00-00 00:00:00')" .
				" AND (" . $alias . ".char_key<>'' OR " . $alias . ".char_key IS NULL)" .
				" AND (" . $alias . ".deleted='' OR " . $alias . ".deleted IS NULL)" .
				(($c_publish && !$s_session['preview']) ? " AND (" . $alias . ".publish<>'' OR " . $alias . ".publish<>'' IS NULL)" : "") . " AND ";
				$sql_where = str_replace("WHERE ", $sql_where_replace, $sql_where);
			}
			if ($c_path_upload) {
				$sql_select = preg_replace('/([ ,])' . $alias . '.file_([0-9])/', '\1REPLACE(' . $alias . '.file_\2,\'../../upload/\',\'' . $c_path_upload . '\') AS file_\2', $sql_select);
			}
		}
	} else {
		// Sem Alias
		preg_match_all("([ ,]+[".$db_prefix."][^ ,]+)", $sql_from, $out, PREG_PATTERN_ORDER);
		foreach ($out[0] as $key=>$value) {
			if (strpos($value, $db_prefix."_tipos")!==false) {
				$sql_where = str_replace("WHERE ","WHERE mostrar<>'' AND (deleted_tipo='' OR deleted_tipo IS NULL) AND ", $sql_where);
			} elseif (strpos($value, $db_prefix . $lang->prefix . '_arquivos') !== false || strpos($value, $db_prefix . '_arquivos') !== false) {
				$sql_where = str_replace("WHERE ","WHERE mostrar<>'' AND (deleted LIKE '' OR deleted IS NULL) AND ", $sql_where);
			} else {
				$sql_where = str_replace("WHERE ","WHERE date_publish<='" . $DbNow . "' AND char_key<>'' AND (deleted LIKE '' OR deleted IS NULL)" . (($c_publish && !$s_session['preview']) ? " AND (publish<>'' OR publish IS NULL)" : "") . " AND ", $sql_where);
			}
		}
		if ($c_path_upload) {
			$sql_select = preg_replace('/([ ,])file_([0-9])/','\1REPLACE(file_\2,\'../../upload/\',\''.$c_path_upload.'\') AS file_\2', $sql_select);
		}
	}
	// Join
	$sql = $sql_select . $sql_from . $sql_where . $sql_final;
	// Debug - After SQL injection
	$debugger->showSql($sql, $sql_debug);
	
	// Return
	if ($debugger->active) $debugger->startTime();
	if($sql_db){
		if(isset($numrows) && isset($offset))
			$rs_pre = $sql_db->SelectLimit($sql, $numrows, $offset) or die(jp7_debug($db->ErrorMsg(), $sql));
		else
			$rs_pre = $sql_db->Execute($sql) or die(jp7_debug($sql_db->ErrorMsg(), $sql));
	} else{
		if (isset($numrows) && isset($offset))
			$rs_pre = $db->SelectLimit($sql, $numrows, $offset) or die(jp7_debug($db->ErrorMsg(), $sql));
		else
			$rs_pre = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(), $sql));
	}
	if ($debugger->active) $debugger->addLog($sql, 'sql', $debugger->getTime($_GET['debug_sql']));
	
	if ($rs && $sql) eval("global \$" . $rs . ";\$" . $rs . "=\$rs_pre;");
	else return $rs_pre;
}

/**
 * Gets the name of a type from its ID.
 *
 * @param int $id_tipo ID of the type.
 * @param bool $nolang If <tt>TRUE</tt> it will return the name regardless of the current language, the default value is <tt>FALSE</tt>.
 * @return string|bool If $id_tipo is numeric it is returned the name of the type, if it evaluates as <tt>FALSE</tt> it is returned <tt>FALSE</tt>, otherwise it is returned "Tipos".
 * @author JP
 * @version (2008/01/09)
 */
function interadmin_tipos_nome($id_tipo,$nolang=FALSE){
	if(!$id_tipo)return FALSE;
	elseif(is_numeric($id_tipo)){
		global $db;
		global $db_prefix;
		global $lang;
		$sql = "SELECT nome,nome".$lang->prefix." AS nome_lang FROM ".$db_prefix."_tipos WHERE id_tipo=".$id_tipo;
		$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(), $sql));
		$row=$rs->FetchNextObj();
		$nome=($row->nome_lang&&!$nolang)?$row->nome_lang:$row->nome;
		$rs->Close();
		return $nome;
	}else{
		return "Tipos";
	}
}

/**
 * Creates a list from values on the database.
 *
 * @param string $table Name of the table containing the itens.
 * @param int $id_tipo ID of the type.
 * @param int $id ID of the current item.
 * @param string $type Type of the list, the available values are: "combo" or "list", the default value is "list".
 * @param string $order SQL string to be placed after the "ORDER BY" statement, the default value is "int_key,date_publish,varchar_key".
 * @param string $field Name of the field which will be used as label on the list, the default value is "varchar_key".
 * @param string $sql_where Additional SQL string to be placed after the "WHERE" statement, it must start with "AND ", the default value is "".
 * @param bool $seo.
 * @global ADOConnection
 * @global bool
 * @global string
 * @return string Generated HTML code for a combobox or a list.
 * @author JP
 * @version (2009/06/13)
 */
function interadmin_list($table,$id_tipo,$id,$type="list",$order="int_key,date_publish,varchar_key",$field="varchar_key",$sql_where="",$seo=FALSE) {
	global $db, $s_session, $l_selecione, $c_publish;
	//global $id;
	if($type=="list"){
		$S="".
		"<div class=\"lista\">\n".
		"<ul class=\"nivel-3\">\n";
	}elseif($type=="combo"){
		$S="".
		"<option value=\"\">".$l_selecione."</option>\n".
		"<option value=\"\">--------------------</option>\n";
	}
	$sql = "SELECT id,".$field." AS field FROM ".$table.
	" WHERE id_tipo=".$id_tipo.
	" AND char_key<>''".
	(($s_session['preview'] || !$c_publish)?"":" AND publish<>''").
	" AND (deleted='' OR deleted IS NULL)".
	" AND date_publish<='".date("Y/m/d H:i:s")."'".
	$sql_where.
	" ORDER BY ".$order;
	$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
	while ($row = $rs->FetchNextObj()) {
		if($seo){
			if($type=="combo")$S.="<option value=\"".toSeo($row->field)."\"".(($row->id==$id)?" selected=\"selected\" class=\"on\"":"").">".toHTML($row->field)."</option>\n";
			else $S.="<li".(($row->id==$id)?" class=\"on\"":"")."><a href=\"?id=".$row->id."\">".toHTML($row->field)."</a></li>\n";
		}else{
			if($type=="combo")$S.="<option value=\"".$row->id."\"".(($row->id==$id)?" selected=\"selected\" class=\"on\"":"").">".toHTML($row->field)."</option>\n";
			else $S.="<li".(($row->id==$id)?" class=\"on\"":"")."><a href=\"?id=".$row->id."\">".toHTML($row->field)."</a></li>\n";
		}
	}
	$rs->Close();
	if($type=="list"){
		$S.="".
		"</ul>\n".
		"</div>\n";
	}
	return $S;
}


/**
 * Alias for jp7_fields_values().
 *
 * @see jp7_fields_values()
 * @version (2006/08/24)
 */
function interadmin_fields_values($param_0,$param_1="",$param_2="",$param_3=""){
	return jp7_fields_values($param_0,$param_1,$param_2,$param_3);
}

/**
 * Gets values from a specified record on the database. It has 3 behaviors as explained on the parameters' description.
 *
 * @param int|string $param_0 If it is numeric, it will be the ID value, otherwise it will be the name of the table.
 * @param int|string $param_1 If $param_0 is numeric it will be the name of the fields, if $param_0 is not numeric and it is numeric, it will be the ID value, otherwise it will be the name of the key field.  
 * @param string $param_2 If $param_0 is not numeric and $param_1 is numeric, it will be the name of the fields, if both are not numeric it will be the ID value.
 * @param string $param_3 If $param_0 and $param_1 are not numeric, it will be the name of the fields.
 * @param bool $OOP If <tt>TRUE</tt> an object will be returned even when there is only one result, the default value is <tt>FALSE</tt>.
 * @global ADOConnection
 * @global string
 * @global string
 * @return mixed Returns an object containing the values. If there is only one value it returns the value itself, except if $OOP is <tt>TRUE</tt>.
 * @author JP
 * @todo ($GLOBALS["jp7_app"]=='intermail') will not be TRUE, since the previous condition ($GLOBALS['db_type']) is TRUE on the Intermail.
 * @version (2008/09/17)
 */
function jp7_fields_values($param_0,$param_1="",$param_2="",$param_3="",$OOP = FALSE){
	global $db;
	global $s_session;
	// Force objects as strings (eg.: select_key, etc.)
	if (is_object($param_0)) $param_0 = strval($param_0);
	if (is_object($param_1)) $param_1 = strval($param_1);
	if (is_numeric($param_0)) {
		// ($id,$field)
		global $db_prefix, $lang;
		$table=$db_prefix.$lang->prefix;
		$table_id_name="id";
		$table_id_value=$param_0;
		$fields=$param_1;
	} elseif (is_numeric($param_1)) {
		// ($table,$id,$field)
		$table = $param_0;
		$table_id_name = "id";
		$table_id_value = $param_1;
		$fields = $param_2;
	} else {
		// ($table,$table_id_name,$table_id_value,$field)
		$table=$param_0;
		$table_id_name=$param_1;
		$table_id_value=$param_2;
		$fields=$param_3;
	}
	
	if(!$fields)$fields="varchar_key";
	if(is_array($fields)){
		$fields_arr = $fields;
		$fields	= implode(',', $fields_arr);
	} else {
		$fields_arr = explode(',', $fields);
	}
	if ($table_id_value) {
		$sql = "SELECT ".$fields.
		" FROM ".$table.
		" WHERE ".$table_id_name."='".$table_id_value."'";
		if (!$GLOBALS['jp7_app'] && strpos($table, '_tipos') === false) {
			$sql .= "" .
			(($GLOBALS['c_publish']&&!$s_session['preview']) ? " AND publish <> ''" : "") .
			" AND (deleted = '' OR deleted IS NULL)" .
			" AND date_publish <= '".date("Y/m/d H:i:s")."'";
		}
		if ($GLOBALS['db_type']) {
			$rs = $db->Execute($sql)or die(jp7_debug($db->ErrorMsg(), $sql));
			if ($row = $rs->FetchNextObj()) {
				if (count($fields_arr) > 1 || $OOP) {
					foreach ($fields_arr as $field) {
						$O->$field = $row->$field;
					}
				} else $O = $row->$fields;
			}
			$rs->Close();
			return $O;
		} else {
			$rs = ($GLOBALS["jp7_app"]=='intermail') ? $db-Execute($sql) : interadmin_mysql_query($sql);
			if ($row = $rs->FetchNextObj()) {
				if (count($fields_arr) > 1) {
					foreach ($fields_arr as $field){
						$O->$field = $row->$field;
					}
				} else $O = $row->$fields;
			}
			$rs->Close();
			return $O;
		}
	}
}

/**
 * Gets the ID of a record on the database from its "varchar_key" and "id_tipo" values.
 *
 * @param string $field_value Value of the field.
 * @param int $id_tipo Value of the field "id_tipo" (Optional).
 * @param string $field_name Name of the field (Optional).
 * @global ADOConnection
 * @global string
 * @global string
 * @return int Value of the field "id", which is the ID of the record.
 * @author JP
 * @version (2008/11/12)
 */
function jp7_id_value($field_value, $id_tipo = 0, $field_name = 'varchar_key') {
	global $db;
	global $db_prefix;
	global $lang;
	$table = $db_prefix . $lang->prefix;
	$sql = "SELECT id FROM " . $table . " WHERE" .
	" " . $field_name . "='" . $field_value . "'" .
	(($id_tipo) ? " AND id_tipo=" . $id_tipo : "");
	$rs = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(), $sql));
	if ($row = $rs->FetchNextObj()) {
		$I = $row->id;
	}
	$rs->Close();
	return $I;
}

/**
 * class jp7_lang
 *
 * @author JP
 * @version (2007/08/08)
 * @package 7lib
 * @subpackage jp7_lang
 */
class jp7_lang{
	/**
	 * Checks the current language.
	 *
	 * @param string $lang Current language, the default value is "".
	 * @param bool $force If <tt>TRUE</tt> it skips the check and $lang becomes the current language, the default value is <tt>FALSE</tt>.
	 * @global string
	 * @global string
	 * @return jp7_lang Object with the following properties: $this->lang, $this->prefix, $this->path and $this->path_2.
	 * @author JP
	 * @version (2006/09/12)
	 */
	function jp7_lang($lang = '', $force = FALSE) {
		global $c_lang_default;
		if (!$lang) $lang = $c_lang_default;
		if ($force) $this->lang = $lang;
		else{
			global $c_path;
			//global $c_site;
			$this->lang=($_SERVER['REQUEST_URI'])?$_SERVER['REQUEST_URI']:$_SERVER['SCRIPT_NAME'];
			if($_SERVER['QUERY_STRING']){
				$pos1=strpos($this->lang,$_SERVER['QUERY_STRING']);
				if($pos1!==false)$this->lang=substr($this->lang,0,$pos1);
			}
			$this->lang = explode("/",$this->lang);
			//if($c_path){ // Old Way
				$path_size = explode("/",$c_path);
				$path_size = count($path_size);
				//$this->lang=$this->lang[$path_size]; // Old Way
				$this->lang = $this->lang[count($this->lang)-3]; // For Hotsites
			//}else $this->lang=$this->lang[1]; // Old Way
			$this->lang = str_replace("_","",$this->lang); // Apache Redirect
		}
		$langs = array('de', 'en', 'es', 'fr', 'jp', 'pt', 'pt-br'); 
		//if(!$this->lang||$this->lang=="pt-br"||$this->lang=="site"||$this->lang==$c_site||$this->lang=="hotsites"||$this->lang=="_hotsites"||$this->lang=="intranet"||$this->lang=="extranet"||$this->lang=="wap"){
		if (!in_array($this->lang, $langs) || $this->lang == $c_lang_default || !$c_lang_default) {
			$this->lang = $lang;
			$this->prefix = "";
			$this->path = "";
			$this->path_url = "site/";
		} else {
			$this->prefix = "_" . $this->lang;
			$this->path = $this->lang . "/";
			$this->path_url = $this->path;
		}
		$this->path_2 = $this->path_url; // Replace later (?)
	}
	/**
	 * Creates a link for the current page on another language.
	 *
	 * @param string $new_lang Language the link will use.
	 * @global string
	 * @global string
	 * @return string Link pointing to the current page on the given language.
	 * @author Carlos
	 * @version (2008/06/26)
	 */
	function getUri($new_lang, $uri = '') {
		global $c_url, $c_lang_default;
		$newLang = new jp7_lang($new_lang, TRUE);
		if (!$uri) $uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		// Separates Query String from Uri
		$uri_parts = explode('?', $uri);
		if ($uri_parts[1]) {
			$uri = $uri_parts[0];
			$querystring_arr = explode('&', $uri_parts[1]);
			foreach($querystring_arr as $value) {
				$arr = explode('=', $value);
				if ($arr[0] != 'id') $values[] = $arr[0] . '=' . $arr[1];
			}
			if ($values) $querystring = '?' . implode('&', (array) $values);
		}
		// Home
		$uri_lang = jp7_path(str_replace($c_url, '', $uri));
		if ($c_url == $uri || $uri_lang == $this->path_url) return $c_url . (($newLang->path_url == 'site/') ? '' : $newLang->path_url) . $querystring;
		// Default
		else return str_replace($c_url . $this->path_url, $c_url . $newLang->path_url, $uri . $querystring);
	}
}

/**
 * class interadmin_tipos
 *
 * @author Thiago
 * @version (2007/07/10)
 * @package 7lib
 * @subpackage interadmin_tipos
 * @deprecated It will be incorporated and suplanted by InterAdminTipos
 */
class interadmin_tipos{
	/**
	 * Gets data of the specified type from the database, and does the same with all of its parent types recursively.
	 *
	 * @param int $id_tipo ID of the type.
	 * @global ADOConnection
	 * @global string
	 * @global jp7_lang
	 * @global string
	 * @return NULL
	 */
	function interadmin_tipos_tipos($id_tipo){
		global $db, $db_prefix, $lang, $c_lang_default;
		settype($id_tipo,'integer');
		$sql = "SELECT parent_id_tipo,model_id_tipo,nome,nome".(($lang->lang!=$c_lang_default)?"_".$lang->lang:"")." AS nome_lang,template,menu,busca,restrito,admin FROM ".$db_prefix."_tipos WHERE id_tipo=".$id_tipo;
		$rs = interadmin_query($sql);
		while ($row = $rs->FetchNextObj()) {
			$this->id_tipo[]=$id_tipo;
			$this->model_id_tipo[]=$row->model_id_tipo;
			$this->nome[]=($row->nome_lang)?$row->nome_lang:$row->nome;
			$this->nome_original[]=$row->nome;
			$this->nome_id[]=toId($row->nome);
			$this->template[]=$row->template;
			$this->menu[]=$row->menu;
			$this->busca[]=$row->busca;
			$this->restrito[]=$row->restrito;
			$this->admin[]=$row->admin;
			$this->interadmin_tipos_tipos($row->parent_id_tipo);
		}
		$rs->Close();
	}
	/**
 	 * Finds the type of a record by its ID, gets its data from the database, and does the same with all of its parent types recursively.
 	 *
	 * @param int $id_tipo ID of the type.
	 * @param int $id ID of the record (optional), it overrides the value of $id_tipo with the record's id_tipo.
	 * @param bool $replaceGlobals If <tt>TRUE</tt> the global $id_tipo is replaced by the local $id_tipo, the default value is <tt>FALSE</tt>.
	 * @global ADOConnection
	 * @global string
	 * @global string
	 * @global string
	 * @todo Check if the "Parent Id" and "Grand Parent Id" code are working properly, since they are replacing $id_tipo it might not bring the children data.
	 * @return interadmin_tipos
	 */
	function __construct($id_tipo,$id=0,$replaceGlobals=FALSE){
		global $db, $db_prefix, $lang, $id_nome, $implicit_parents_names;
		// Id
		if($id&&is_numeric($id)){
			$sql = "SELECT id_tipo,parent_id,varchar_key FROM ".$db_prefix.$lang->prefix." WHERE id=".$id;
			$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
			while ($row = $rs->FetchNextObj()) {
				$id_tipo=$row->id_tipo;
				$parent_id=$row->parent_id;
				$id_nome=$row->varchar_key;
			}
			$rs->Close();
		}
		// Parent Id
		if($parent_id&&is_numeric($parent_id)){
			$sql = "SELECT id_tipo,parent_id FROM ".$db_prefix.$lang->prefix." WHERE id=".$parent_id;
			$rs = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(),$sql));
			while ($row = $rs->FetchNextObj()) {
				$id_tipo=$row->id_tipo;
				$grand_parent_id=$row->parent_id;
			}
			$rs->Close();
		}
		// Grand Parent Id
		if($grand_parent_id&&is_numeric($grand_parent_id)){
			$sql = "SELECT id_tipo FROM ".$db_prefix.$lang->prefix." WHERE id=".$grand_parent_id;
			$rs = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(),$sql));
			while ($row = $rs->FetchNextObj()) {
				$id_tipo=$row->id_tipo;
			}
			$rs->Close();
		}
		// Tipos
		if($id_tipo&&is_numeric($id_tipo)){
			if($replaceGlobals)$GLOBALS["id_tipo"]=$id_tipo;
			$this->interadmin_tipos_tipos($id_tipo);
			if($this->id_tipo){
				$this->id_tipo=array_reverse($this->id_tipo);
				$this->model_id_tipo=array_reverse($this->model_id_tipo);
				$this->nome=array_reverse($this->nome);
				$this->nome_original=array_reverse($this->nome_original);
				$this->nome_id=array_reverse($this->nome_id);
				$this->template=array_reverse($this->template);
				$this->menu=array_reverse($this->menu);
				$this->busca=array_reverse($this->busca);
				$this->restrito=array_reverse($this->restrito);
				$this->admin=array_reverse($this->admin);
				$this->i=count($this->id_tipo);
				$this->path=implode("/",$this->nome_id);
				$this->path_title=implode("/",$this->nome);
			}
		}
		$path_seo = '';
		$path_seo_arr = array();
		foreach ((array) $this->nome as $key=>$nome) {
			if (!in_array($nome, (array)$implicit_parents_names)) {
				$path_seo = toSeo($nome); //. (($key < count($this->nome) - 1) ? '/' : '');
				$path_seo_arr[] = $path_seo;
				$this->path_seo[] = '/' . $GLOBALS['c_path'] . implode('/', $path_seo_arr);
			} else {
				$this->path_seo[] = '/' . $GLOBALS['c_path'] . toSeo($nome);
			}
		}
	}
}

/**
 * Gets the id_tipo from the record's ID or from its parent_id_tipo.
 *
 * @param int $id Record's ID.
 * @param int $parent_id_tipo Parent type's ID (optional).
 * @param int $model_id_tipo Model type's ID (optional).
 * @global ADOConnection
 * @global string
 * @global string
 * @return int|NULL If $id is specified it returns its id_tipo, otherwise it returns the first child's id_tipo for the $parent_id_tipo. If both fail nothing is returned.
 * @version (2007/05/23)
 */
function interadmin_id_tipo($id="",$parent_id_tipo=0,$model_id_tipo=0){
	global $db;
	global $db_prefix;
	global $lang;
	if($id){
		$sql = "SELECT id_tipo FROM ".$db_prefix.$lang->prefix.
		" WHERE id=".$id;
	}else{
		$sql = "SELECT id_tipo FROM ".$db_prefix."_tipos".
		" WHERE parent_id_tipo=".$parent_id_tipo.
		(($model_id_tipo)?" AND model_id_tipo=".$model_id_tipo:"").
		" ORDER BY ordem,nome";
	}
	$sql.=" LIMIT 1";
	$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(), $sql));
	if ($row=$rs->FetchNextObj()){
		return $row->id_tipo;
	}
	$rs->Close();
}

/**
 * class interadmin_cabecalho
 *
 * @version (2006/11/29)
 * @package 7lib
 * @subpackage interadmin_cabecalho
 */
class interadmin_cabecalho{
	/**
	 * Gets text and images of the specified type.
	 *
	 * @param int $i Index of the type on the global $tipos, the default value is 0.
	 * @param int $model_id_tipo Value of the model_id_tipo of this type, used to find the correct type, default value is 5.
	 * @param string $check Fields which will have their values checked to make sure they are not empty, names separated by comma (,), the default value is "file_1,file_2".
	 * @param bool $rand The default value is <tt>FALSE</tt>.
	 * @global ADOConnection
	 * @global string
	 * @global string
	 * @return interadmin_cabecalho 
	 * @version (2006/11/29)
	 */
	function interadmin_cabecalho($i=0,$model_id_tipo=5,$check="file_1,file_2",$rand=FALSE){
		global $db;
		global $db_prefix;
		global $tipos;
		if($id_tipo=interadmin_id_tipo(0,$tipos->id_tipo[$i],$model_id_tipo)){
			$sql = "SELECT varchar_key,varchar_1,varchar_2,file_1,file_2 FROM ".$db_prefix.$lang->prefix.
			" WHERE id_tipo=".$id_tipo.
			" AND char_key<>''".
			" AND publish<>''".
			" AND deleted=''".
			" ORDER BY int_key,date_publish DESC";
			$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(), $sql));
			if($rand)$rand=rand(1,$rs->RecordCount());
			$j=1;
			while ($row = $rs->FetchNextObj()) {
				if($j==$rand||!$rand){
					$this->varchar_key=$row->varchar_key;
					$this->varchar_1=$row->varchar_1;
					$this->varchar_2=$row->varchar_2;
					$this->varchar_3=$row->varchar_3;
					$this->file_1=$row->file_1;
					$this->file_2=$row->file_2;
					break;
				}
				$j++;
			}
			$rs->Close();
			$check_arr=explode(",",$check);
			foreach($check_arr as $check_field){
				eval("\$check_value=\$this->".$check_field.";");
				if($check_value){
					$check_ok=true;
					break;
				}
			}
		}
		if(!$check_ok&&$i)$this->interadmin_cabecalho($i-1,$model_id_tipo,$check,$rand);
	}
}

/**
 * Generates the code for inserting Flash(.swf) files, or an image when its not a flash file.
 *
 * @param string $src URL of the Flash file.
 * @param int $w Width.
 * @param int $h Height.
 * @param string $alt Alternative text for the image.
 * @param string $id ID of the "object" tag.
 * @param string $xtra Additional parameters for the "object" tag.
 * @param string $parameters Additional "param" tags. 
 * @global Browser
 * @return string Generated HTML code.
 * @version (2005/11/18)
 */
function jp7_flash($src,$w,$h,$alt="",$id="",$xtra="",$parameters=""){
	$pos1=strpos($src,"?");
	$ext=($pos1)?substr($src,0,$pos1):$src;
	$pos1=strrpos($ext,".")+1;
	$ext=substr($ext,$pos1);
	if($ext=="swf"){
		if(!$parameters)$parameters=array(wmode=>"transparent");
		global $is;
		foreach($parameters as $key=>$value){
			$S2.="<param name=\"".$key."\" value=\"".$value."\" />\n";
		}
		$S="".
		"<object".(($id)?" id=\"".$id."\"":"").
		" type=\"application/x-shockwave-flash\"".
		//(($is->ie&&$is->win)?" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\"":"").
		" data=\"".$src."\"".
		(($w&&$h)? " width=\"".$w."\" height=\"".$h."\"":"").
		(($xtra)?" ".$xtra:"").">\n";
		$S.="".
		"<param name=\"pluginurl\" value=\"http://www.macromedia.com/go/getflashplayer\" />\n".
		"<param name=\"movie\" value=\"".$src."\" />\n".
		"<param name=\"quality\" value=\"high\" />\n".
		$S2.
		"</object>";
		if($is->ie&&$is->win){
			if($id){
				$S.="".
				"<script type=\"text/vbscript\" language=\"vbscript\">\n".
				"on error resume next\n".
				"sub ".$id."_FSCommand(ByVal command,ByVal args)\n".
				"call flash_DoFSCommand(command,args)\n".
				"end sub\n".
				"</script>\n";
			}
		}else{
			if($id){
				$S.="".
				"<script type=\"text/javascript\">\n".
				"function ".$id."_DoFSCommand(command,args){\n".
				"flash_DoFSCommand(command,args)\n".
				"}\n".
				"</script>\n";
			}
		}
		return $S;
	}else{
		if($w=="100%"||$h=="100%"){
			$w="";
			$h="";
		}
		return "<img src=\"".$src."\"".(($w&&$h)? " width=".$w." height=".$h:"")." border=\"0\" alt=\"".$alt."\"".(($id)?" name=\"".$id."\"":"").(($xtra)?" ".$xtra:"")."/>";
	}
}

// jp7_interlog (2005/06/09)
function jp7_interlog($id_cliente, $host = 'jp7.com.br', $db_name_interlog = 'interlog'){
	global $c_site;
	global $c_server_type;
	global $db;
	global $db_name;
	//if(!$SERVER_ADDR)$SERVER_ADDR=$LOCAL_ADDR;
	if($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && strpos($REMOTE_ADDR, '192.168') !== 0) {
	//if($REMOTE_ADDR!="127.0.0.1"&&strpos($REMOTE_ADDR,"192.168")!==0&&($c_server_type=="Principal"||$host=="localhost")){
		if($host == "localhost"){
			if(!$db){
				$only_info=true;
				include "inc/connection_open.php";
				$db = mysql_connect($db_host, $db_user, $db_pass) or print(mysql_error());
			}
			if($db){
				$servidor_date = date("Y/m/d H:i:s");
				$separador = "{;}";
				$dados = $id_cliente . $separador . $_SERVER['SERVER_ADDR'] . $separador . $servidor_date . $separador . $_SERVER['REMOTE_ADDR'] . $separador . $_SERVER['HTTP_USER_AGENT'];
				mysql_select_db($db_name_interlog, $db);
				mysql_query("INSERT INTO interlog_" . $c_site . " (dados) VALUES ('".$dados."')", $db)or print(mysql_error());
				mysql_select_db($db_name, $db);
			}
			if ($only_info) mysql_close($db);
		}else{
			ob_start();
			readfile('http://' . $host . '/interlog/site/aplicacao/acessos_inserir_ok.php?id_cliente=' . $id_cliente . '&servidor_ip=' . $_SERVER['SERVER_ADDR'] . '&visitante_ip=' . $_SERVER['REMOTE_ADDR'] . '&visitante_useragent=' . urlencode($_SERVER['HTTP_USER_AGENT']));
			ob_end_clean();
		}
	}
}

/**
 * Adds a trailing slash on a path, in case it doesn't have one.
 *
 * @param string $S Input String (Path, URL).
 * @param bool $reverse If <tt>TRUE</tt> the trailing slash is removed instead of added, the default value is <tt>FALSE</tt>.
 * @return string String with a trailing slash.
 * @version (2003/08/25)
 */
function jp7_path($S, $reverse = FALSE){
	if ($reverse) return (substr($S, strlen($S) - 1) == '/') ? substr($S, 0, strlen($S) - 1) : $S; 
	else return (strrpos($S,'/') + 1 == strlen($S) || !$S) ? $S : $S . '/';
}

/**
 * Attempts to find the root directory.
 *
 * @global string
 * @global bool
 * @global string
 * @return string Root directory.
 * @version (2009/03/20)
 */
function jp7_doc_root(){
 	global $PATH_INFO, $c_jp7, $c_path;
	$S = @ini_get('doc_root');
	if(!$S) $S = $_SERVER['DOCUMENT_ROOT'];
	if(!$S){
		$S = dirname($_SERVER['PATH_TRANSLATED']);
		if($c_jp7){
			$S = str_replace("\\","/",$S);
			$S = str_replace("//","/",$S);
			$S = substr($S,0,strpos($S,dirname($PATH_INFO)));
		}
	}
	if(!$S){
		$S = realpath("./");
		$S = substr($c_root,0, ($c_path) ? strpos($S,$c_path) : strpos($S,"site"));
	}
	$S = jp7_path($S);
	return $S;
}

/**
 * Attempts to include a file from two levels above and, if it fails, tries from the root.
 *
 * @param string $file Filename which will be included. e.g. "inc/example.php".
 * @global Jp7_Debugger
 * @return NULL
 * @version (2008/06/13)
 * @deprecated Instead of using this function use "include jp7_path_find('folder/filename.php');"
 */
function jp7_include($file){
	global $debugger;
	if ($debugger) $debugger->showFilename($file);
	$include = @include $file;
	if (!$include) {
		if (!@include '../../' . $file) @include jp7_doc_root() . $file;
	}
}

/**
 * Attempts to find a file on the directories above the current directory and, if it fails, it points to the root.
 *
 * @param string $file Filename.
 * @param bool $autoload Is called by __autoload.
 * @global Jp7_Debugger
 * @staticvar int $path_levels Number of paths from the root to the current folder.
 * @return string Path to the file.
 * @author JP, Carlos
 * @version (2009/02/25)
 */
function jp7_path_find($file, $autoload = false) {
	global $debugger;
	static $path_levels;
	if (!$path_levels) $path_levels = count(explode('/', $_SERVER['PHP_SELF'])) - 1; // Total de pastas.
	for ($i = 0; $i <= $path_levels; $i++) {
		($i) ? $path .= '../' : $path = '';
		if ($ok = @file_exists($path . $file)) break;
	}
	if (!$ok && !$autoload) {
		// Necess·rio para localizaÁ„o de includes em templates
		$path = jp7_path($GLOBALS['c_doc_root'], TRUE) . dirname($_SERVER['REQUEST_URI']) . '/';
		$ok = @file_exists($path . $file);
	}
	if (!$ok && !$autoload) {
		if (strpos($file,'/head.php') !== FALSE) return jp7_path_find(str_replace('/head.php', '/7.head.php', $file));
		if ($GLOBALS['c_template'] && strpos($file, '../../inc/') !== FALSE) return jp7_path_find(str_replace('../../inc/', '../../../_templates/' . $GLOBALS['c_template'] . '/inc/', $file));
		$path = '';
		if (@file_exists(jp7_doc_root() . $file)) $path = jp7_doc_root();
	}
	return ($debugger && !$autoload) ? $debugger->showFilename($path . $file) : $path . $file;
}

/**
 * Gets the extension of a file.
 *
 * @param string $S Filename.
 * @return string Extension of the file or "---" if no extension is found.
 * @version (2003/08/25)
 */
function jp7_extension($S) {
	$path_parts = pathinfo($S);
	$ext = trim($path_parts['extension'] . ' ');
	return (!$ext) ? "---" : $ext;
}

/**
 * Formats and sends an e-mail message.
 *
 * @param string $to Receiver, or receivers of the mail.
 * @param string $subject Subject of the email to be sent. 
 * @param string $message Message to be sent.
 * @param string $headers String to be inserted at the begin of the email header (only if $html is <tt>FALSE</tt>).
 * @param string $parameters Additional parameters to the program configured to use when sending mail using the sendmail_path configuration setting.
 * @param string $template Path to the template file.
 * @param bool $html If <tt>FALSE</tt> will send the message on the text-only format. The default value is <tt>TRUE</tt>.
 * @param string $attachments
 * @see http://www.php.net/manual/en/function.mail.php
 * @global bool
 * @return bool Returns <tt>TRUE</tt> if the mail was successfully accepted for delivery, <tt>FALSE</tt> otherwise.
 * @todo The parameter $attachments is not used.
 * @author JP
 * @version (2007/08/01)
 */
function jp7_mail($to,$subject,$message,$headers="",$parameters="",$template="",$html=TRUE,$attachments=""){
	global $debug;
	// TEXT
	if(strpos($message,"<br>")!==false){
		$text_hr="";
		for($i = 0;$i<80;$i++){
			$text_hr.="-";
		}
		$message_text=str_replace("\r","",$message);
		$message_text=str_replace("\n","",$message_text);
		$message_text=str_replace("&nbsp;"," ",$message_text);
		$message_text=str_replace("<hr size=1 color=\"#666666\">",$text_hr."\r\n",$message_text);
		$message_text=str_replace("<br>","\r\n",$message_text);
	}
	$message_text=strip_tags($message_text);
	// HTML
	if($html){
		$message_html=str_replace("\r\n","\n",$message); // PC to Linux
		$message_html=str_replace("\r","\n",$message_html); // Mac to Linux
		$message_html=str_replace("\n","\r\n",$message_html); // Linux to Mail Format
		if(strpos($message_html,"<br>")===false){
			$message_html=str_replace("\r\n","<br>\r\n",$message_html); // Linux to Mail Format
		}
		if($template){
			@ini_set("allow_url_fopen","1");
			if((!dirname($template)||dirname($template)==".")&&@ini_get("allow_url_fopen")){
				$template="http://".$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME'])."/".$template;
			}
			if($pos1=strpos($template,"?")){
				//$template=substr($template,0,$pos1+1).urlencode(substr($template,$pos1+1));
				$template=str_replace(" ","%20",$template);
			}
			
			//valida usu·rio logado e caso o template inicie em http
			if($_SERVER["PHP_AUTH_USER"]){
				$template=str_replace("http://","http://".$_SERVER["PHP_AUTH_USER"].":".$_SERVER["PHP_AUTH_PW"]."@", $template);
			}
			
			if (strpos($template,"http://") === 0){
				if(function_exists("file_get_contents")){
					$template=file_get_contents($template);
				}else{
					ob_start();
					readfile($template);
					$template=ob_get_contents();
					ob_end_clean();
				}
			} else {
				$template = file_get_contents('http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $template);
			}
			
			//echo "template: ".$template;
			$message_html=str_replace("%MESSAGE%",$message_html,$template);
		}
		$message_html=str_replace("=","=3D",$message_html);
		// Boundaries
		$mime_boundary_1="==Multipart_Boundary_x".md5(time()+1)."x";
		$mime_boundary_2="==Multipart_Boundary_x".md5(time()+2)."x";
		// Headers
		$headers="MIME-Version: 1.0\r\n".
		$headers.
		"Return-Errors-To: sites@jp7.com.br\r\n".
		"Content-Type: multipart/alternative;\r\n".
		"	boundary=\"".$mime_boundary_2."\"";
		//"Content-Type: multipart/mixed;\r\n".
		//"	boundary=\"".$mime_boundary_1."\"";
		// Message
	 	$message="This is a multi-part message in MIME format.\r\n\r\n".
		//"--".$mime_boundary_1."\r\n".
		//"Content-Type: multipart/alternative;\r\n".
		//"	boundary=\"".$mime_boundary_2."\"\r\n\r\n".
		// TEXT
		"--".$mime_boundary_2."\r\n".
		"Content-Type: text/plain; charset=\"iso-8859-1\"\r\n".
		"Content-Transfer-Encoding: quoted-printable\r\n\r\n".
		$message_text."\r\n\r\n".
		// HTML
		"--".$mime_boundary_2."\r\n".
		"Content-Type: text/html; charset=\"iso-8859-1\"\r\n".
		"Content-Transfer-Encoding: quoted-printable\r\n\r\n".
		$message_html."\r\n\r\n".
		// Footer
		"--".$mime_boundary_2."--\r\n\r\n";
	}else{
		// Headers
		$headers.=
		"Return-Errors-To: sites@jp7.com.br\r\n".
		"Content-Type: text/plain";// charset=\"iso-8859-1\"\r\n".
		//"Content-Transfer-Encoding: quoted-printable";
		// Message
		$message=$message_text;
	}
	// Encode
	$subject=jp7_encode_mimeheader($subject);
	// Check CRLF
	if(strpos($_ENV["OS"],"Windows")===false||!$_ENV["OS"]){
		$message=str_replace("\r\n","\n",$message);
		$headers=str_replace("\r\n","\n",$headers);
	}
	// Send
	if($GLOBALS['c_server_type']!="Principal")$to="debug@jp7.com.br";
	$mail=mail($to,$subject,$message,$headers,$parameters);
	if(!$mail)$mail=mail($to,$subject,$message,$headers); // Safe Mode
	if($debug)echo "jp7_mail(".htmlentities($to)."): ".$mail."<br>";
	return $mail;
}

/**
 * Write text to the image using TrueType fonts, shadows are supported.
 *
 * @param string $filename_src Path to the PNG image.
 * @param string $filename_dst The path to save the file to.
 * @param float $size The font size. Depending on your version of GD, this should be specified as the pixel size (GD1) or point size (GD2).
 * @param float $angle The angle in degrees, with 0 degrees being left-to-right reading text. Higher values represent a counter-clockwise rotation.
 * @param int|string $x The coordinates given by x and y will define the basepoint of the first character (roughly the lower-left corner of the character). The available values are an integer value or the strings "center", right" and "trim".
 * @param int|string $y The y-ordinate. This sets the position of the fonts baseline, not the very bottom of the character. The available values are an integer value or "center".
 * @param string $col RGB string with its values separated by comma, e.g. "255,0,0" would be the color red.
 * @param string $fontfile The path to the TrueType font you wish to use.
 * @param string $text The text string in UTF-8 encoding. If a character is used in the string which is not supported by the font, a hollow rectangle will replace the character. 
 * @param string $padding Padding for the text, using CSS-like format, the default value is "0 0 0 0".
 * @param array|bool $shadow Array containing "color" (RGB string separated by comma),  "x" and "y" (the coordinates of the shadow), the default value is <tt>FALSE</tt>.
 * @param string $antialiasing If it receives the char "-" (minus) the antialiasing is turned off, the default value is "".
 * @param bol $truecolor If <tt>TRUE</tt> Os PNGs ser„o tratados com alpha. The default value is <tt>FALSE</tt>.
 * @return NULL
 * @version (2006/04/07)
 */
function jp7_image_text($filename_src,$filename_dst,$size,$angle,$x,$y,$col,$fontfile,$text,$padding="0 0 0 0",$shadow=FALSE,$antialiasing="",$truecolor=false){
	$im=imagecreatefrompng($filename_src);
	if ($truecolor) imagesavealpha($im, true);
	$col_arr=explode(",",$col);
	$col_arr=imagecolorallocate($im,$col_arr[0],$col_arr[1],$col_arr[2]);
	if($x!=="center"&&$shadow){
		$shadow_color=explode(",",$shadow[color]);
		if(function_exists('imagecolorallocatealpha'))$shadow_color=imagecolorallocatealpha($im,$shadow_color[0],$shadow_color[1],$shadow_color[2],$shadow_color[3]);
		else $shadow_color=imagecolorallocate($im,$shadow_color[0],$shadow_color[1],$shadow_color[2]);
		imagettftext($im,$size,$angle,$x+$shadow[x],$y+$shadow[y],$shadow_color,$fontfile,$text);
	}
	$padding=explode(" ",$padding);
	imagettftext($im,$size,$angle,($x==="center"||$x==="right"||$x==="trim")?$padding[3]:$x,($y==="center")?0:$y,$antialiasing.$col_arr,$fontfile,$text);
	imagepng($im,$filename_dst);
	imagedestroy($im);
	// Center
	if($x==="center"||$y==="center"){
		$im=imagecreatefrompng($filename_dst);
		if ($truecolor) imagesavealpha($im, true);
		$center=imagettfbbox($size,$angle,$fontfile,$text);
		if($x==="center"){
			$x=$center[4]+1;
			$x=(imagesx($im)-$x-$padding[3])/2;
		}
		if($y=="center"){
			$y=$center[5]+1;
			$y=(imagesy($im)-$y-$padding[0])/2;
		}
		if($x!=="center")jp7_image_text($filename_src,$filename_dst,$size,$angle,$x,$y,$col,$fontfile,$text,"",$shadow,$antialiasing);
		imagedestroy($im);
	// Right
	}elseif($x==="right"){
		$im=imagecreatefrompng($filename_dst);
		if ($truecolor) imagesavealpha($im, true);
		$right=imagettfbbox($size,$angle,$fontfile,$text);
		if($x==="right"){
			$x=$right[4];
			$x=(imagesx($im)-$x-$padding[1]);
		}
		if($x!=="right")jp7_image_text($filename_src,$filename_dst,$size,$angle,$x,$y,$col,$fontfile,$text,"",$shadow,$antialiasing);
		imagedestroy($im);
	// Trim
	}elseif($x==="trim"){
		$im=imagecreatefrompng($filename_dst);
		if ($truecolor) imagesavealpha($im, true);
		$x=imagettfbbox($size,$angle,$fontfile,$text);
		$x=$x[4]+1;
		if($x!=="trim"){
			if ($truecolor) {
				$im2=imagecreatetruecolor($x+$padding[1]+$padding[3],imagesy($im));
				imagealphablending($im2, false);
				imagecopy($im2,$im,0,0,0,0,$x+$padding[1]+$padding[3],imagesy($im));
				imagesavealpha($im2, true);
				imagepng($im2,$filename_dst);
				imagedestroy($im2);
			} else {
				$im2=imagecreate($x+$padding[1]+$padding[3],imagesy($im));
				$im_bg=imagecolorsforindex($im,imagecolorat($im,1,1));
				$im_bg=imagecolorallocate($im2,$im_bg["red"],$im_bg["green"],$im_bg["blue"]);
				imagefill($im2,0,0,$im_bg);
				imagecolortransparent($im2,$im_bg);
				imagecopymerge($im2,$im,0,0,0,0,$x+$padding[1]+$padding[3],imagesy($im),100);
				imagepng($im2,$filename_dst);
				imagedestroy($im2);
			}
		}
		imagedestroy($im);
	}
}

/**
 * Resizes an image to the specified dimensions.
 *
 * @param resource $im_src An image resource, returned by one of the image creation functions, such as imagecreatefromjpeg().
 * @param string $src Path to the source image. 
 * @param string $dst Path to the destination image. 
 * @param int $w Destination width. 
 * @param int $h Destination height. 
 * @param int $q Ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file). The default value is 90. 
 * @param int $s Maximum filesize in bytes, from this size the quality is changed to the $q value (used only if the destination dimensions are bigger). The default value is 10000000 (10MB).
 * @return NULL
 * @version (2008/08/29)
 */
function jp7_resizeImage($im_src, $src, $dst, $w, $h, $q = 90, $s = 10000000) {
	$c_gd = function_exists('imagecreatefromjpeg');
	// Check Size and Orientation (Horizontal x Vertical)
	if ($c_gd) {
		// GD Get Size
		$src_w = imagesx($im_src);
		$src_h = imagesy($im_src);
	} else {
		// Magick Get Size
		$command = '/usr/bin/identify -verbose ' . $src;
		exec($command, $a, $b);
		$src_geometry = split('x', substr($a[2], strpos($a[2], ':') + 2));
		$src_w = $src_geometry[0];
		$src_h = $src_geometry[1];
	}
	// Source and destination with the same dimensions or the same proportions (just resize if needed)
	if (($src_w == $w && $src_h == $h) || ($src_w / $src_h == $w / $h)) {
		$dst_w = $w;
		$dst_h = $h;
	// Destination is square (with same width and height - crop if needed)
	} elseif ($w == $h) {
		$dst_w = $w;
		$dst_h = $h;
		if ($src_w > $src_h) $src_w = $src_h;
		else $src_h = $src_w;
	// The image is resized until it gets the maximum width or height (without crop)
	} else {
		$pre_dst_w = intval(round(($h * $src_w) / $src_h));
		$pre_dst_h = intval(round(($w * $src_h) / $src_w));
		if ($pre_dst_h <= $h){
			$dst_w = $w;
			$dst_h = $pre_dst_h;
		} else {
			$dst_h = $h;
			$dst_w = $pre_dst_w;
		}
	}
	// Checks if destination image is bigger than source image
	if($dst_w>=$src_w&&$dst_h>=$src_h){
		// No-Resize and Check Weight
		if (filesize($src) > $s) {
			$im_dst = $im_src;
			if ($c_gd) {
				// GD Convert Quality
				imagejpeg($im_dst, $dst, $q);
			} else {
				// Magick Convert Quality
				$command = "/usr/bin/convert ".$src." -quality ".$q." +profile '*' ".$dst;
				exec($command, $a, $b);
			}
		}else{
			if(jp7_extension($src)=="gif")$dst=str_replace(".jpg",".gif",$dst);
			copy($src,$dst);
		}
	}else{
		if($c_gd){
			// GD Resize
			$im_dst=imagecreatetruecolor($dst_w,$dst_h);
			imagecopyresampled($im_dst,$im_src,0,0,0,0,$dst_w,$dst_h,$src_w,$src_h);
			imagejpeg($im_dst,$dst,$q);
			imagedestroy($im_dst);
		}else{
			// Magick Resize
			$command="/usr/bin/convert ".$src." -resize '".$dst_w."x".$dst_h."!' -quality ".$q." +profile '*' ".$dst;
			exec($command,$a,$b);
		}
	}
}

/**
 * Attempts to encode a given string by the MIME header encoding scheme. 
 *
 * @param string $S The string to be encoded.
 * @param string $charset Specifies the name of the character set in which the string is represented in, the default value is "iso-8859-1".
 * @param string $transfer_encoding Specifies the scheme of MIME encoding. It should be either "B" (Base64) or "Q" (Quoted-Printable), the default value is "Q".
 * @see http://www.php.net/manual/en/function.mb-encode-mimeheader.php
 * @return string If mb_encode_mimeheader() exists it returns the converted version of the string represented in ASCII, otherwise it returns the input string.
 * @version (2005/12/08)
 */
function jp7_encode_mimeheader($S,$charset="iso-8859-1",$transfer_encoding="Q"){
	return (function_exists("mb_encode_mimeheader"))?mb_encode_mimeheader($S,$charset,$transfer_encoding,(strpos($_ENV["OS"],"Windows")===false||!$_ENV["OS"])?"\n":"\r\n"):$S;
}

/**
 * Performs common tasks on index pages, caching and redirecting to the home page.
 *
 * @param string $lang Current language.
 * @global Browser
 * @global string
 * @global bool
 * @global bool
 * @global string
 * @return NULL
 * @author JP
 * @version (2008/01/11)
 * @deprecated jp7_index() is not needed since the redirects are managed by RewriteRule in .htaccess
 */
function jp7_index($lang=""){
	session_start();
	//global $HTTP_ACCEPT;
	global $is, $path, $publish, $s_session, $c_lang_default;
	$path=dirname($_SERVER["SCRIPT_NAME"]);
	$path=jp7_path("http://".$_SERVER['HTTP_HOST'].$path);
	// Publish Check
	$admin_time=@filemtime("interadmin.log");
	$index_time=@filemtime("site/home/index_P.htm");
	if($admin_time>$index_time||date("d")!=date("d",$index_time))$publish=true;
	// Redirect
	//if(strpos($_SERVER['HTTP_ACCEPT'],"/vnd.wap")!==false)header("Location: ".$path."wap/home/index.php");
	//elseif($is->v<4&&!$is->robot)header("Location: /_default/oldbrowser.htm");
	//else{
		$path=$path.(($lang&&$lang!=$c_lang_default)?$lang:"site")."/home/".(($publish||!$admin_time||!$index_time)?"index.php":"index_P.htm").(($s_session['preview'])?"?s_interadmin_preview=".$s_session['preview']:"");
		@ini_set("allow_url_fopen","1");
		//if(!@include $path.(($s_session['preview'])?"&":"?")."HTTP_USER_AGENT=".urlencode($_SERVER['HTTP_USER_AGENT']))header("Location: ".$path);
		if(!@readfile($path.(($s_session['preview'])?"&":"?")."HTTP_USER_AGENT=".urlencode($_SERVER['HTTP_USER_AGENT'])))header("Location: ".$path);
	//}
}

/**
 * Checks if one of the specified hosts is the current host.
 *
 * @param mixed $hosts List of hosts as array or as a string separated by comma (,).
 * @return bool Returns <tt>TRUE</tt> if the current host is found.
 * @author JP
 * @version (2008/07/22)
 */
function jp7_host($hosts){
	if (!is_array($hosts)) $hosts = explode(',', $hosts);
	foreach($hosts as $host){
		if (strpos($_SERVER['HTTP_HOST'], $host) !== FALSE){
			return TRUE;
			exit;
		}
	}
}

/**
 * Checks if its c_jp7 to return the filename.
 *
 * @param string $filename Input string.
 * @return string If the global variable "c_jpj" is evaluated as <tt>TRUE</tt> it returns the input string, otherwise it returns an empty string.
 * @deprecated jp7_path_find() has replaced this function when it comes to show filenames for debugging
 */
function getFileName($filename){
	return ($GLOBALS["c_jp7"])?$filename:"";
}

/**
 * Gets file size
 *
 * @param string $file Path to the file.
 * @return string Size of the file in KB or MB.
 */
function jp7_file_size($file){
	$file = ceil(@filesize($file) / 1000);
	$file = ($file < 1000) ? ceil($file) . 'KB' : round($file / 1000, 1) . 'MB';
	return $file;
}

/**
 * Gets and formats the backtrace of an error, optionally sends it on an e-mail and shows user friendly maintenance screen.
 *
 * @param string $msgErro Error message, the default is <tt>NULL</tt>.
 * @param string $sql SQL it tried to execute, the default is <tt>NULL</tt>.
 * @param array $traceArr Debugging data, like the return of debug_backtrace().
 * @global Jp7_Debugger
 * @return string HTML formatted backtrace.
 */
function jp7_debug($msgErro = NULL, $sql = NULL, $traceArr = NULL) {
	global $debugger;
	global $s_interadmin_cliente, $jp7_app;
	global $c_site, $c_server_type;
	if (!$traceArr) $traceArr = debug_backtrace();
	$backtrace = $debugger->getBacktrace($msgErro, $sql, $traceArr);
	$nome_app = ($jp7_app) ? $jp7_app : 'Site';
	//Envia email e exibe tela de manutenÁ„o
	if($c_server_type == 'Principal') {
		if (trim($c_site)) $cliente = $c_site;
		elseif (trim($s_interadmin_cliente)) $cliente = $s_interadmin_cliente;
		$subject = '['. $cliente . '][' . $nome_app . '][Erro]';
		$message = 'Ocorreram erros no ' . $nome_app . ' - ' . $cliente . '<br />' . $backtrace;
		$to = 'debug+' . $cliente . '@jp7.com.br';
		$headers = 'To: ' . $to . " <" . $to . ">\r\n";
		$headers .= 'From: ' . $to . " <" . $to . ">\r\n";
		$parameters = '';
		//$template="form_htm.php";
		$html = TRUE;
		jp7_mail($to, $subject, $message, $headers, $parameters, $template, $html);
		if($c_server_type == 'Principal' && (!$jp7_app || $jp7_cache)) {
			$backtrace = 'Ocorreu um erro ao tentar acessar esta p·gina, se o erro persistir envie um email para <a href="debug@jp7.com.br">debug@jp7.com.br</a>';
			header('Location: /_default/index_manutencao.htm');
			//Caso nao funcione o header, tenta por javascript	?>
            <script language="javascript" type="text/javascript">
			document.location.href="/_default/index_manutencao.htm";
			</script>
            <?
			exit();
		}
	}
	return $backtrace;	
}

/**
 * XOR Encrypts a given string with a given key phrase.
 *
 * @param string $InputString Input string
 * @param string $KeyPhrase Key phrase
 * @return string Encrypted string
 */    
function XOREncryption($InputString, $KeyPhrase){
    $KeyPhraseLength = strlen($KeyPhrase);
    for ($i = 0; $i < strlen($InputString); $i++){   // Loop trough input string
        $rPos = $i % $KeyPhraseLength; // Get key phrase character position
        $r = ord($InputString[$i]) ^ ord($KeyPhrase[$rPos]); // Magic happens here:
        $InputString[$i] = chr($r); // Replace characters
    }
    return $InputString;
}

/**
 * Encrypts a given string with a given key phrase using XOR.
 *
 * @param string $InputString Input string
 * @param string $KeyPhrase Key phrase
 * @return string Encrypted string
 */
function XOREncrypt($InputString, $KeyPhrase){
    $InputString = XOREncryption($InputString, $KeyPhrase);
    $InputString = urlencode($InputString);
    return $InputString;
}

/**
 * Decrypts a given string with a given key phrase using XOR.
 *
 * @param string $InputString Input string
 * @param string $KeyPhrase Key phrase
 * @return string Decrypted string
 */
function XORDecrypt($InputString, $KeyPhrase){
    $InputString = urldecode($InputString);
    $InputString = XOREncryption($InputString, $KeyPhrase);
    return $InputString;
}

// moveFiles (2003/03/21)
function moveFiles($from_path,$to_path){
	if(!file_exists($to_path))mkdir($to_path,0777);
	$this_path=getcwd();
	if(is_dir($from_path)){
		chdir($from_path);
		$handle=opendir("."); 
		while(($file=readdir($handle))!==false){ 
			if(($file!=".")&&($file!="..")){ 
				if(is_dir($file)){ 
					@copyDir($from_path."/".$file,$to_path."/".$file);
					chdir($from_path);
				}
				if(is_file($file)){
					copy($from_path."/".$file,$to_path."/".$file);
					unlink($from_path."/".$file);
				}
			}
		}
		closedir($handle);
	}
	chdir($this_path);
}

/**
 * Splits the string into an array. The difference from explode() is that jp7_explode() unsets empty values.
 * 
 * @param string $separator
 * @param string $string
 * @param bool $useTrim If set the function will trim() each part of the string. Defaults to <tt>TRUE</tt>.
 * @return array Array of parts withuot any empty value.
 */
function jp7_explode($separator, $string, $useTrim = true) {
	$array = explode($separator, $string);
	foreach($array as $key => $value) {
		if ($useTrim) $value = trim($value);
		if (!$value) unset($array[$key]);
		else $array[$key] = $value;
	}
	return $array;
}

/**
 * Joins the array into a string. The difference from implode() is that jp7_implode() discards empty values.
 * 
 * @param string $separator
 * @param string $string
 * @param bool $useTrim If set the function will trim() each part of the string. Defaults to <tt>TRUE</tt>.
 * @return array Array of parts withuot any empty value.
 */
function jp7_implode($separator, $array, $useTrim = true) {
	if (!$array) return $array;
	foreach($array as $key => $value) {
		if ($useTrim) $value = trim($value);
		if (!$value) unset($array[$key]);
		else $array[$key] = $value;
	}
	$string = implode($separator, $array);
	return $string;
}

/**
 * Same as file_exists, the difference is that it takes include_path in consideration.
 *
 * @param string $filename Relative path to the file or directory.
 * @return bool Returns TRUE if the file or directory specified by filename exists on any of the directories listed on include_path; FALSE otherwise.
 */
function jp7_file_exists($filename) {
	$include_paths = explode(PATH_SEPARATOR, get_include_path());
	foreach ($include_paths as $include_path) {
		if (file_exists($include_path . '/' . $filename)) {
			return true;
		}
	}
	return false;
}

/**
 * Alias of {@link Krumo::dump()}
 *
 * @param mixed $data,...
 * @see Krumo::dump()
 */
function krumo() {
    $_ = func_get_args();
    return call_user_func_array(
        array('Krumo', 'dump'), $_
    );
}

/**
 * Works as a bootstrap for custom pages inside /APP_config/CLIENT or /CLIENT/APP.
 * Parses the URI and sets the include_path.
 * 
 * @return string Filename to be included.
 */
function interadmin_bootstrap() {
	global $config;

	$urlArr = explode('/', $_SERVER['REQUEST_URI']);

	if (preg_match('/_config/', $urlArr[1])) {
		// APP_config/CLIENTE
		$jp7_app = $_GET['jp7_app'] = str_replace('_config', '', $urlArr[1]);
        $cliente = $_GET['cliente'] = $urlArr[2];
        $url = str_replace('/' . $jp7_app . '_config/' . $cliente . '/', '', $_SERVER['REQUEST_URI']);
	} else {
		// CLIENTE/APP
		$jp7_app = $_GET['jp7_app'] = $urlArr[2];
        $cliente = $_GET['cliente'] = $urlArr[1];
        $url = str_replace('/' . $cliente . '/' . $jp7_app . '/', '', $_SERVER['REQUEST_URI']);
	}

	// Retira a query string
	$url = preg_replace('/([^?]*)(.*)/', '\1', $url);

	if (!$url) {
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $jp7_app . '/' . $cliente);
		exit;
	}
	
	set_include_path('.' . PATH_SEPARATOR . jp7_doc_root() . $jp7_app);
	if ($jp7_app != 'interadmin') {
		set_include_path(get_include_path() . PATH_SEPARATOR . jp7_doc_root() . 'interadmin');
	}
	return $url;
}

/**
 * Checks if an executable program exists. On Windows it works only for .exe files.
 * Searchs for the executable file inside the directories on the %PATH% variable.
 * 
 * @param $executable Name of the file without the extension (.exe), e.g. "svn".
 * @return bool
 */
function jp7_is_executable($executable) {
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$comando = 'for %G in ("%path:;=" "%") do @IF EXIST %G/' . $executable . '.exe echo 1';
	} else { 
		$comando = 'type -P ' . $executable;
	}
	return (bool) @shell_exec($comando);
}

/**
 * Checks the current version of a package using a call to SVN executable.
 * The version is cached on a file called: $packageDir/.version
 * 
 * @param string $packageDir Name of the package on SVN repository, defaults to 'interadmin'.
 * @param string $format Format of the output. Defaults to "Vers„o {release} (Build {build})".
 * @return string Formatted string.
 */
function interadmin_get_version($packageDir = 'interadmin', $format = 'Vers„o {release} (Build {build})')
{
	global $c_doc_root;
	
	$cacheFile = $c_doc_root . $packageDir . '/.version';
	if (@is_file($cacheFile)) {
        // If .version was saved this day or SVN is not available, keep .version cache
		if (date('Y-m-d') === date('Y-m-d', @filemtime($cacheFile)) || !jp7_is_executable('svn')) {
            $version = unserialize(file_get_contents($cacheFile));
        }
	}
	
	if (!$version) {
		$comando = 'svn info "' . $c_doc_root . $packageDir . '"';
		$svninfo = explode("\n", shell_exec($comando));
		
		$version = new stdClass();
		$version->release = reset(preg_grep('/^URL:(.*)/', $svninfo));
		$version->release = preg_replace('~URL:(.*)' . $packageDir . '/~', '', $version->release);
		if (strpos($version->release, 'tags') === 0) {
			$version->release = preg_replace('~tags/release-([0-9.]*)(-crypt)?~', '$1', $version->release);			
		}
		$version->build = reset(preg_grep('/^Rev(.*)/', $svninfo));
		$version->build = preg_replace('~Rev(.*): (.*)~', '$2', $version->build);
		file_put_contents($cacheFile, serialize($version));
	}
	$retorno = str_replace('{release}', $version->release, $format);
	$retorno = str_replace('{build}', $version->build, $retorno);
	return $retorno;
}

function array_trim($var) {
	return trim($var);
}
