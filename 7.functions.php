<?php

/**
 * Takes off diacritics and empty spaces from a string, if $tofile is <tt>FALSE</tt> (default) the case is changed to lowercase.
 *
 * @param string $S String to be formatted.
 * @param bool $tofile Sets whether it will be used for a filename or not, <tt>FALSE</tt> is the default value.
 * @param string $separador	Separator used to replace empty spaces.
 *
 * @return string Formatted string.
 *
 * @version (2006/01/18)
 */
function toId($string, $tofile = false, $separador = '')
{
    // Check if there are diacritics before replacing them
    if (preg_match('/[^a-zA-Z0-9-\/ _.,]/', $string)) {
        $string = preg_replace('/[áàãâäÁÀÃÂÄª]/u', 'a', $string);
        $string = preg_replace('/[éèêëÉÈÊË&]/u', 'e', $string);
        $string = preg_replace('/[íìîïÍÌÎÏ]/u', 'i', $string);
        $string = preg_replace('/[óòõôöÓÒÕÔÖº]/u', 'o', $string);
        $string = preg_replace('/[úùûüÚÙÛÜ]/u', 'u', $string);
        $string = preg_replace('/[çÇ]/u', 'c', $string);
        $string = preg_replace('/[ñÑ]/u', 'n', $string);
    }
    if ($tofile) {
        $string = preg_replace('/[^a-zA-Z0-9_]/u', '_', $string);
    } else {
        $string = preg_replace('/[^a-zA-Z0-9_]+/u', $separador, $string);
        $string = trim(mb_strtolower($string), $separador);
    }
    if ($separador) {
        $string = str_replace('_', $separador, $string);
    } else {
        $string = preg_replace('/[\/-]/u', '_', $string);
    }
    return $string;
}

/**
 * Takes off diacritics from a string and replace special characters and empty spaces by '-'.
 *
 * @param string $S String to be formatted.
 *
 * @return string Formatted string.
 *
 * @author JP
 *
 * @version (2008/06/12) update by Carlos Rodrigues
 */
function toSeo($string)
{
    return toId($string, false, '-');
}

/**
 * Alias for toSeo().
 */
function toSlug($string)
{
    return toSeo($string);
}

/**
 * Generates a SQL WHERE statement with REGEXP for 'decoding' the toSeo() function.
 *
 * @param string $field Field where the data will be searched, e.g. varchar_key.
 * @param string $str String to be formatted and searched.
 * @param string $regexp Optional REGEXP string, the default value is '[^\d\w]?'.
 *
 * @return string Formatted SQL WHERE statement with a REGEXP.
 *
 * @author Carlos Rodrigues
 *
 * @version (2008/06/12)
 * @deprecated
 */
function toSeoSearch($field, $str, $regexp = '[^[:alnum:]]*')
{
    $sql_where = $regexp;
    for ($i = 0; $i < mb_strlen($str); $i++) {
        $char = $str[$i];
        $char = str_replace('a', '[aáàãâäª]', $char);
        $char = str_replace('e', '[eéèêë&]', $char);
        $char = str_replace('i', '[iíìîï]', $char);
        $char = str_replace('o', '[oóòõôöº]', $char);
        $char = str_replace('u', '[uúùûü]', $char);
        $char = str_replace('c', '[cç]', $char);
        $char = str_replace('n', '[nñ]', $char);
        $sql_where .= $char.$regexp;
    }

    return 'REPLACE('.$field.",' ','') REGEXP '^".$sql_where."$'";
}

/**
 * @deprecated
 */
function wap_toHTML($S)
{
    return Jp7_Deprecated::wap_toHTML($S);
}

/**
 * Quotes a string to be sent to the database. e.g. mysql becomes 'mysql'.
 *
 * @param string $S The input string.
 * @param bool $force_magic_quotes_gpc If TRUE the string will be quoted even if 'magic_quotes_gpc' is not active.
 *
 * @global ADOConnection
 *
 * @return string Quoted string.
 *
 * @version (2003/08/25)
 * @deprecated
 */
function toBase($S, $force_magic_quotes_gpc = false)
{
    global $db;
    if (mb_strlen($S)) {
        $S = $db->qstr($S, get_magic_quotes_gpc() && !$force_magic_quotes_gpc); //trata as aspas. Ex.: 'mysql' fica \'mysql\'
        $S = trim($S);
    } else {
        $S = "''";
    }

    return $S;
}

/**
 * Replaces double and single quotes so they can be used inside an HTML element's attribute. e.g. \'test\' becomes &#39;test&#39;.
 *
 * @param string $S String to be formatted.
 *
 * @return string Formatted string.
 *
 * @version (2004/06/14)
 * @deprecated
 */
function toForm($S)
{
    $S = str_replace("\'", '&#39;', $S);// Bug LocaWeb e JavaScript
    $S = str_replace('\"', '"', $S);// Bug LocaWeb
    return stripslashes(str_replace('"', '&quot;', $S));
}

/**
 * Formats an string to be used as HTML text, strips slashes and replaces values.
 *
 * @param string $S String to be formatted.
 * @param bool $HTML If <tt>FALSE</tt> (default) the line breaks are replaced by <br />
 * @param bool $busca_replace If <tt>TRUE</tt> the function uses the regex string ($busca_varchar or $busca_text, passed by globals) to replace values. <tt>FALSE</tt> is the default value.
 *
 * @global string
 * @global string
 *
 * @return string Formatted string.
 *
 * @version (2004/06/14)
 * @deprecated
 */
function toHTML($S, $HTML = false, $busca_replace = false)
{
    global $busca_varchar, $busca_text;
    $busca = ($busca_varchar) ? $busca_varchar : $busca_text;
    if (mb_strlen($S)) {
        if (!$HTML) {
            $S = str_replace(chr(13), ' <br /> ', $S);
        }
        //elseif(strpos(mb_strtolower($S),"<p>")===false)$S="<p>".$S."</p>";
        $S = str_replace("\'", "'", $S);// Bug LocaWeb
        $S = str_replace("''", "'", $S);// Bug LocaWeb
        $S = str_replace('\"', '"', $S);// Bug LocaWeb
        if ($busca_replace && $busca) {
            $S = preg_replace("/[^@\.]".$busca."[^@\.]/i", ' <span class="font-search">'.mb_strtoupper($busca).'</span> ', $S);
        }

        return stripslashes($S);
    }
}

/**
 * Formats a string to be used inside a javascript. Replaces \" by &quot; and ' by \'.
 *
 * @param string $S String to be formatted.
 *
 * @return string Formatted string.
 *
 * @version (2004/05/31)
 * @deprecated
 */
function toScript($S)
{
    $S = str_replace("\r", '\r', $S);
    $S = str_replace("\n", '\n', $S);
    $S = str_replace('"', '&quot;', $S);
    $S = str_replace("'", "\'", $S);

    return $S;
}

/**
 * Formats a string to be used inside a parameter. Replaces \" by &quot; and line breaks by empty spaces (" ").
 *
 * @param string $S String to be formatted.
 *
 * @return string Formatted string.
 *
 * @version (2007/06/25)
 *
 * @author JP
 */
function toParam($S)
{
    $S = str_replace('"', '&quot;', $S);
    $S = str_replace("\n", ' ', $S);
    $S = str_replace("\r", ' ', $S);
    $S = str_replace(chr(13), ' ', $S);
    $S = str_replace(chr(11), ' ', $S);

    return $S;
}

/**
 * Formats a string to be used inside a XML.
 *
 * @param string $S String to be formatted.
 *
 * @return string Formatted string.
 *
 * @version (2008/12/05)
 *
 * @author JP7
 */
function toXml($S)
{
    return str_replace(['&', '"', "'", '<', '>', '’'], ['&amp;', '&quot;', '&apos;', '&lt;', '&gt;', '&apos;'], $S);
}

/*
 * Converts Hex string into binary string.
 *
 * @param string $S String to be converted.
 * @return string Binary string.
 * @version (2007/01/22)
 * @author JP
 */
if (!function_exists('hex2bin')) {
    function hex2bin($S)
    {
        return pack('H'.mb_strlen($S), $S);
    }
}

/**
 * Encrypts a string using a key.
 *
 * @param string $S String that will be encrypted.
 * @param string $key Key with which the data will be encrypted, the key will be required to decrypt it as well, the default value is the md5 hash of $_SERVER["HTTP_HOST"].
 * @param string $cipher One of the MCRYPT_ciphername constants of the name of the algorithm, the default value is <tt>MCRYPT_RIJNDAEL_128</tt>.
 * @param string $mode One of the MCRYPT_MODE_modename constants, the default value is <tt>MCRYPT_MODE_ECB</tt>.
 *
 * @return string Encrypted string.
 *
 * @version (2007/04/19)
 *
 * @author JP
 * @deprecated
 */
function jp7_encrypt($S, $key = '', $cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_ECB)
{
    if (!$key) {
        $key = md5($_SERVER['HTTP_HOST']);
    }
    $key = str_pad($key, 16);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher, $mode), MCRYPT_RAND);

    return bin2hex(mcrypt_encrypt($cipher, $key, $S, $mode, $iv));
}

/**
 * Decrypts a string using a key.
 *
 * @param string $S Encrypted string.
 * @param string $key Key with which the data was encrypted, the default value is the md5 hash of $_SERVER["HTTP_HOST"].
 * @param string $cipher One of the MCRYPT_ciphername constants of the name of the algorithm, the default value is <tt>MCRYPT_RIJNDAEL_128</tt>.
 * @param string $mode One of the MCRYPT_MODE_modename constants, the default value is <tt>MCRYPT_MODE_ECB</tt>.
 *
 * @return string Decrypted string.
 *
 * @version (2007/04/19)
 *
 * @author JP
 * @deprecated
 */
function jp7_decrypt($S, $key = '', $cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_ECB)
{
    if (!$key) {
        $key = md5($_SERVER['HTTP_HOST']);
    }
    $key = str_pad($key, 16);
    $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher, $mode), MCRYPT_RAND);

    return trim(mcrypt_decrypt($cipher, $key, hex2bin($S), $mode, $iv), "\0");
}

function toXHTML($S)
{
    return Jp7_Deprecated::toXHTML($S);
}

/**
 * Checks if the referer page is the same as it was expected to be.
 *
 * @param string $S Expected referer page's URL.
 * @param string $protocol Protocol used, the default value is "http".
 *
 * @return bool <tt>TRUE</tt> if the referer is the expected page, <tt>FALSE</tt> if not.
 *
 * @version (2008/05/19)
 */
function checkReferer($S, $protocol = 'http')
{
    /*
    while(strpos($S,"../")!==false){
    }
    */
    if (!dirname($S) || dirname($S) == '.') {
        $parent_dirname = dirname(dirname($_SERVER['REQUEST_URI']));
        if ($parent_dirname == '/') {
            $parent_dirname = '';
        }

        $dirname = dirname($_SERVER['REQUEST_URI']);
        if ($dirname == '/') {
            $dirname = '';
        }

        $S_parent = $protocol.'://'.$_SERVER['HTTP_HOST'].$parent_dirname.'/'.$S;
        $S = $protocol.'://'.$_SERVER['HTTP_HOST'].$dirname.'/'.$S;
    }

    return (strpos($_SERVER['HTTP_REFERER'], $S) === 0 || strpos($_SERVER['HTTP_REFERER'], $S_parent) === 0);
}

/**
 * Shrinks the input string and adds "..." if it is larger than the maximum length, the input string is not changed if its shorter.
 *
 * @param string $S Input string.
 * @param int $length Max. lenght of the output string.
 *
 * @return string Shrunk string.
 *
 * @version (2008/07/04)
 *
 * @global string
 * @global string
 */
function jp7_string_left($S, $length)
{
    global $s_session, $c_lang;
    if ($c_lang) {
        foreach ($c_lang as $item) {
            if ($item[0] == $s_session['lang'] && $item[2]) {
                $length = $length * 8;
            } // Check if language uses entities for characters (eg.: japanese)
        }
    }

    return (mb_strlen($S) > $length) ? mb_substr($S, 0, $length).'...' : $S;
}

/**
 * Truncates text.
 *
 * Cuts a string to the length of $length and replaces the last characters
 * with the ending if the text is longer than length.
 *
 * @param string  $text String to truncate.
 * @param int $length Length of returned string, including ellipsis.
 * @param string  $ending Ending to be appended to the trimmed string.
 * @param bool $exact If false, $text will not be cut mid-word
 * @param bool $considerHtml If true, HTML tags would be handled correctly
 *
 * @return string Trimmed string.
 */
function jp7_truncate($text, $length = 100, $considerHtml = true, $ending = '...', $exact = true)
{
    if ($considerHtml) {
        // if the plain text is shorter than the maximum length, return the whole text
        if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
            return $text;
        }
        // splits all html-tags to scanable lines
        preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
        $total_length = mb_strlen($ending);
        $open_tags = [];
        $truncate = '';
        foreach ($lines as $line_matchings) {
            // if there is any html-tag in this line, handle it and add it (uncounted) to the output
            if (!empty($line_matchings[1])) {
                // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
                if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
                    // do nothing
                // if tag is a closing tag (f.e. </b>)
                } elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
                    // delete tag from $open_tags list
                    $pos = array_search($tag_matchings[1], $open_tags);
                    if ($pos !== false) {
                        unset($open_tags[$pos]);
                    }
                // if tag is an opening tag (f.e. <b>)
                } elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
                    // add tag to the beginning of $open_tags list
                    array_unshift($open_tags, mb_strtolower($tag_matchings[1]));
                }
                // add html-tag to $truncate'd text
                $truncate .= $line_matchings[1];
            }
            // calculate the length of the plain text part of the line; handle entities as one character
            $content_length = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
            if ($total_length + $content_length > $length) {
                // the number of characters which are left
                $left = $length - $total_length;
                $entities_length = 0;
                // search for html entities
                if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
                    // calculate the real length of all entities in the legal range
                    foreach ($entities[0] as $entity) {
                        if ($entity[1] + 1 - $entities_length <= $left) {
                            $left--;
                            $entities_length += mb_strlen($entity[0]);
                        } else {
                            // no more characters left
                            break;
                        }
                    }
                }
                $truncate .= mb_substr($line_matchings[2], 0, $left + $entities_length);
                // maximum lenght is reached, so get off the loop
                break;
            } else {
                $truncate .= $line_matchings[2];
                $total_length += $content_length;
            }
            // if the maximum length is reached, get off the loop
            if ($total_length >= $length) {
                break;
            }
        }
    } else {
        if (mb_strlen($text) <= $length) {
            return $text;
        } else {
            $truncate = mb_substr($text, 0, $length - mb_strlen($ending));
        }
    }
    // if the words shouldn't be cut in the middle...
    if (!$exact) {
        // ...search the last occurance of a space...
        $spacepos = strrpos($truncate, ' ');
        if (isset($spacepos)) {
            // ...and cut the text in this position
            $truncate = mb_substr($truncate, 0, $spacepos);
        }
    }
    // add the defined ending to the text
    $truncate .= $ending;
    if ($considerHtml) {
        // close all unclosed html-tags
        foreach ($open_tags as $tag) {
            $truncate .= '</'.$tag.'>';
        }
    }

    return $truncate;
}

/**
 * Sets global variables using values from superglobals if "register_globals" is OFF, emulating this feature.
 *
 * @global string
 *
 * @todo Check if this function could be flagged as "deprecated".
 *
 * @version (2007/03/03)
 * @deprecated
 */
function jp7_register_globals()
{
    global $HTTP_HOST;
    if (!@ini_get('register_globals') || !$HTTP_HOST) {
        if (!empty($_SERVER)) {
            foreach ($_SERVER as $key => $value) {
                $GLOBALS[$key] = $_SERVER[$key];
            }
        }
        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                $GLOBALS[$key] = $_GET[$key];
            }
        }
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $GLOBALS[$key] = $_POST[$key];
            }
        }
        if (!empty($_COOKIE)) {
            foreach ($_COOKIE as $key => $value) {
                $GLOBALS[$key] = $_COOKIE[$key];
            }
        }
        if (!empty($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                $GLOBALS[$key] = $_SESSION[$key];
            }
        }
    }
}

/**
 * Creates an alphanumeric password (a-z, 0-9).
 *
 * @param string $length Length of the created password, the default value is 6.
 *
 * @return string Created password.
 *
 * @version (2008/09/25)
 *
 * @author JP
 * @deprecated
 */
function jp7_password($length = 6)
{
    $chars = 'abcdefghijkmnopqrstuvwxyz023456789';
    $S = '';
    for ($i = 0; $i < $length; $i++) {
        $S .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
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
 *
 * @return string|NULL Formatted string or <tt>NULL</tt>.
 *
 * @version (2008/02/06)
 *
 * @author JP
 */
function jp7_print_r($var, $return = false, $hideProtectedVars = false, $varPrefix = '')
{
    return Jp7_Deprecated::jp7_print_r($var, $return, $hideProtectedVars, $varPrefix);
}

/**
 * Splits a time/date into an array.
 *
 * @param string $date String containing a date/time on the format Y-m-d H:i:s or Y/m/d H:i:s.
 *
 * @return array Array containing the following keys: Y, m, M, d, H, i, s and y.
 *
 * @version (2008/05/27)
 */
function jp7_date_split($date)
{
    $date = str_replace(' ', ',', $date);
    $date = str_replace('/', ',', $date);
    $date = str_replace('-', ',', $date);
    $date = str_replace(':', ',', $date);
    $date = explode(',', $date);

    return [
        Y => $date[0],
        m => $date[1],
        M => jp7_date_month($date[1], true),
        F => jp7_date_month($date[1]),
        d => $date[2],
        H => $date[3],
        i => $date[4],
        s => $date[5],
        y => mb_substr($date[0], 2),
    ];
}

/**
 * Returns date formatted according to given format.
 *
 * @param string $date Date/time string.
 * @param string $format Format using: "Y", "m", "M", "d", "H", "i", "s" or "y". The default value is "d/m/Y", when english language is active the "d/m" is automatically replaced by "m/d".
 *
 * @global string
 *
 * @return string|NULL Returns formatted date or <tt>NULL</tt> if no date is given.
 *
 * @version (2010/02/08)
 */
function jp7_date_format($date, $format = 'd/m/Y')
{
    global $jp7_app;
    if ($jp7_app) {
        $lang = new jp7_lang('pt-br', true);
    } else {
        global $lang;
    }

    if ($date instanceof Jp7_Date) {
        $date = $date->format('Y-m-d H:m:i');
    }

    if ($date) {
        if ($lang->lang == 'en') {
            $format = str_replace('d/m', 'm/d', $format);
            $format = str_replace('d-m', 'm-d', $format);
        }
        $date = jp7_date_split($date);
        $S = '';
        for ($i = 0;$i < mb_strlen($format);$i++) {
            $x = mb_substr($format, $i, 1);
            $S .= ($date[$x]) ? $date[$x] : $x;
        }

        return $S;
    }
}

/**
 * Returns textual representation for the day of the week, such as Sunday or Saturday. Supports english and portuguese.
 *
 * @param int|string $w A numeric representation of the day of the week (0 for Sunday through 6 for Saturday), or a date/time string.
 * @param string $sigla If <tt>TRUE</tt> returns only the first three letters, the default value is <tt>FALSE</tt>.
 *
 * @global string
 *
 * @return string Textual representation for the day of the week.
 *
 * @version (2006/04/27)
 */
function jp7_date_week($w, $sigla = false)
{
    global $lang;
    switch ($lang->lang) {
        case 'en': $W = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']; break;
        case 'de': $W = ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag']; break;
        case 'es': $W = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado']; break;
        default: $W = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado']; break;
    }
    if (!is_int($w)) {
        $w = date('w', strtotime($w));
    }
    $return = $W[$w];

    return ($sigla) ? mb_substr($return, 0, 3) : $return;
}

/**
 * Returns textual representation of a month, such as January or March. Supports english and portuguese.
 *
 * @param int $m Numeric representation of a month, (1 for January through 12 for December).
 * @param string $sigla If <tt>TRUE</tt> returns only the first three letters, the default value is <tt>FALSE</tt>.
 *
 * @global string
 *
 * @return string Textual representation of a month.
 *
 * @version (2004/06/14)
 */
function jp7_date_month($m, $sigla = false)
{
    global $lang;
    switch ($lang->lang) {
        case 'en':
            $M = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            break;
        case 'de':
            $M = ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'];
            break;
        case 'es':
            $M = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            break;
        default:
            $M = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            break;
    }
    $return = $M[$m - 1];

    return ($sigla) ? mb_substr($return, 0, 3) : $return;
}

/**
 * Calculates the number of months from the start date to the end date.
 *
 * @param string $start Start date, string on the "Y-m-d" format.
 * @param string $end End date, string on the "Y-m-d" format.
 *
 * @return int Number of months.
 *
 * @author Paulo
 *
 * @todo Add functionality to return days and years too, and to take off the time aggregated with a date, like: 2008-10-08 00:01:02.
 *
 * @version (2008/04/15)
 */
function jp7_date_diff($start, $end)
{
    $start = explode('-', $start);
    $start = mktime(0, 0, 0, $start[1], $start[2], $start[0]); // mes / dia / ano (padrao mktime)
    $end = explode('-', $end);
    $end = mktime(0, 0, 0, $end[1], $end[2], $end[0]); // mes / dia / ano (padrao mktime)
    $diff = ($end - $start);
    $diff = explode('-', date('Y-m-d', $diff));
    $diff_r['m'] = $diff[1];

    return $diff_r['m'];
}

/**
 * Splits a telephone number into "ddd", "numero" and "ramal".
 *
 * @param string $tel String containing a telephone number.
 *
 * @return array Array containing "ddd", "numero" and "ramal".
 *
 * @todo Add support for poorly formatted telephones like: "-Ramal:", " R:", " - R:", maybe taking off empty spaces and "-".
 *
 * @version (2004/08/12)
 */
function jp7_tel_split($tel)
{
    $tel = str_replace('(', '', $tel);
    $tel = str_replace(')', ',', $tel);
    $tel = str_replace(' - Ramal: ', ',', $tel);
    $tel = explode(',', $tel);

    return [
        ddd => trim($tel[0]),
        numero => trim($tel[1]),
        ramal => trim($tel[2]),
    ];
}

function jp7_db_select($table, $table_id_name, $table_id_value, $var_prefix = '', $returnValues = false)
{
    return Jp7_Deprecated::jp7_db_select($table, $table_id_name, $table_id_value, $var_prefix, $returnValues);
}

function jp7_db_insert($table, $table_id_name, $table_id_value = 0, $var_prefix = '', $var_check = true, $force_magic_quotes_gpc = false)
{
    return Jp7_Deprecated::jp7_db_insert($table, $table_id_name, $table_id_value, $var_prefix, $var_check, $force_magic_quotes_gpc);
}

/**
 * Creates a checkbox and a hidden field, the hidden field will have a value or not depending on whether the checkbox is checked or not.
 *
 * @param string $name Name of the hidden field.
 * @param string $value Value that the hidden field will have if the checkbox is checked, the default value is "S".
 * @param string $var Name of global variable containing the current value for the hidden field, the default value is "".
 * @param string $readonly Readonly parameter to be inserted on the checkbox. e.g. readonly="readonly"
 * @param string $xtra Additional HTML parameter to be inserted on the checkbox.
 *
 * @return string If $GLOBALS["interadmin_visualizar"] is set it returns "Sim" or "Não", otherwise it returns the created HTML for checkbox and hidden field.
 *
 * @todo Make $readonly a boolean, setting if the field is readonly or not. Check if its better to replace $GLOBALS["interadmin_visualizar"] by global $interadmin_visualizar.
 *
 * @author JP
 *
 * @version (2007/07/13)
 */
function jp7_db_checkbox($name, $value = 'S', $var = '', $readonly = '', $xtra = '', $var_value = null)
{
    if (!$var) {
        $var = $name;
    }
    if (is_null($var_value)) {
        $var_value = $GLOBALS[$var];
    }
    if ($GLOBALS['interadmin_visualizar']) {
        return (($var_value) ? 'Sim' : 'Não');
    } else {
        return ''.
        '<input type="checkbox" name="jp7_db_checkbox_'.$name.'" id="jp7_db_checkbox_'.$name.'" value="'.$value.'"'.(($var_value) ? ' checked="checked"' : '').$readonly." onclick=\"form['".$name."'].value=(checked)?value:''\"".(($xtra) ? ' '.$xtra : '').' />'.
        '<input type="hidden" name="'.$name.'" value="'.(($var_value) ? $value : '').'" />';
    }
}

function jp7_db_update($table, $table_id_name, $table_id_value, $fields)
{
    return Jp7_Deprecated::jp7_db_update($table, $table_id_name, $table_id_value, $fields);
}

/**
 * Creates an array from a given list of fields using Interadmin's format.
 *
 * @param string $campos String containing the fields of a type, fields separated by {;}, parameters separated by {,}.
 *
 * @return array Array of fields with its parameters.
 *
 * @author JP
 *
 * @version (2007/03/10)
 */
function interadmin_tipos_campos($campos)
{
    $campos_parameters = ['tipo', 'nome', 'ajuda', 'tamanho', 'obrigatorio', 'separador', 'xtra', 'lista', 'orderby', 'combo', 'readonly', 'form', 'label', 'permissoes', 'default', 'nome_id'];
    $campos = explode('{;}', $campos);
    for ($i = 0; $i < count($campos); $i++) {
        $parameters = explode('{,}', $campos[$i]);
        if ($parameters[0]) {
            $A[$parameters[0]][ordem] = ($i + 1);
            for ($j = 0; $j < count($parameters); $j++) {
                $A[$parameters[0]][$campos_parameters[$j]] = $parameters[$j];
            }
        }
    }

    return $A;
}

/**
 * Transforma array de campos em string separada por ; e {,} no formato do InterAdmin.
 *
 * @param array $campos
 *
 * @return string
 */
function interadmin_tipos_campos_encode($campos)
{
    $s = '';
    foreach ($campos as $key => $value) {
        unset($value['ordem']);
        $s .= implode('{,}', $value).'{;}';
    }

    return $s;
}

/**
 * Gets an array containing "nome" and "xtra" values of a field on Interadmin.
 *
 * @param string $db_prefix Prefix of the table.
 * @param string $id_tipo ID of the type which will be searched (column "id_tipo").
 * @param string $var_key Name of the field from this type that will be got. e.g. "varchar_key".
 *
 * @global ADOConnection
 * @global string
 * @global int
 *
 * @return array Array containing "nome" and "xtra" values of the field.
 *
 * @version (2004/11/03)
 */
function interadmin_tipos_campo($db_prefix, $id_tipo, $var_key)
{
    global $db, $tipo_campos, $tipo_model_id_tipo;
    $tipo_model_id_tipo = $id_tipo;
    while ($tipo_model_id_tipo) {
        jp7_db_select($db_prefix.'_tipos', 'id_tipo', $tipo_model_id_tipo, 'tipo_');
    }
    $tipo_campos = explode('{;}', $tipo_campos);
    foreach ($tipo_campos as $campo) {
        $campo = explode('{,}', $campo);
        if ($campo[0] == $var_key) {
            return [
                'nome' => $campo[1],
                'xtra' => $campo[6],
            ];
            break;
        }
    }
}

/**
 * Alias for interadmin_query().
 *
 * @deprecated
 * @see interadmin_query()
 *
 * @author JP
 *
 * @version (2007/04/25)
 */
function interadmin_mysql_query($sql, $sql_db = '', $sql_debug = false)
{
    return interadmin_query($sql, $sql_db, $sql_debug);
}

function interadmin_query($sql, $sql_db = '', $sql_debug = false, $numrows = null, $offset = null)
{
    return Jp7_Deprecated::interadmin_query($sql, $sql_db, $sql_debug, $numrows, $offset);
}

/**
 * Gets the name of a type from its ID.
 *
 * @param int $id_tipo ID of the type.
 * @param bool $nolang If <tt>TRUE</tt> it will return the name regardless of the current language, the default value is <tt>FALSE</tt>.
 *
 * @return string|bool If $id_tipo is numeric it is returned the name of the type, if it evaluates as <tt>FALSE</tt> it is returned <tt>FALSE</tt>, otherwise it is returned "Tipos".
 *
 * @author JP
 *
 * @version (2008/01/09)
 */
function interadmin_tipos_nome($id_tipo, $nolang = false)
{
    if (!$id_tipo) {
        return false;
    } elseif (is_numeric($id_tipo)) {
        global $db;
        global $db_prefix;
        global $lang;
        $sql = 'SELECT nome,nome'.$lang->prefix.' AS nome_lang FROM '.$db_prefix.'_tipos WHERE id_tipo='.$id_tipo;
        $rs = $db->Execute($sql);
        if ($rs === false) {
            throw new Jp7_Interadmin_Exception($db->ErrorMsg());
        }
        $row = $rs->FetchNextObj();
        $nome = ($row->nome_lang && !$nolang) ? $row->nome_lang : $row->nome;
        $rs->Close();

        return $nome;
    } else {
        return 'Tipos';
    }
}

function interadmin_list($table, $id_tipo, $id, $type = 'list', $order = 'int_key,date_publish,varchar_key', $field = 'varchar_key', $sql_where = '', $seo = false)
{
    return Jp7_Deprecated::interadmin_list($table, $id_tipo, $id, $type, $order, $field, $sql_where, $seo);
}

/**
 * Alias for jp7_fields_values().
 *
 * @see jp7_fields_values()
 *
 * @version (2006/08/24)
 */
function interadmin_fields_values($param_0, $param_1 = '', $param_2 = '', $param_3 = '')
{
    return jp7_fields_values($param_0, $param_1, $param_2, $param_3);
}

function jp7_fields_values($table_or_id, $field_or_id = '', $id_value = '', $field_name = '', $OOP = false)
{
    return Jp7_Deprecated::jp7_fields_values($table_or_id, $field_or_id, $id_value, $field_name, $OOP);
}

/**
 * Gets the ID of a record on the database from its "varchar_key" and "id_tipo" values.
 *
 * @param string $field_value Value of the field.
 * @param int $id_tipo Value of the field "id_tipo" (Optional).
 * @param string $field_name Name of the field (Optional).
 *
 * @global ADOConnection
 * @global string
 * @global string
 *
 * @return int Value of the field "id", which is the ID of the record.
 *
 * @author JP
 *
 * @version (2008/11/12)
 */
function jp7_id_value($field_value, $id_tipo = 0, $field_name = 'varchar_key')
{
    global $db;
    global $db_prefix;
    global $lang;

    $table = $db_prefix.$lang->prefix;
    $sql = 'SELECT id FROM '.$table.' WHERE'.
        ' '.$field_name."='".$field_value."'".
        (($id_tipo) ? ' AND id_tipo='.$id_tipo : '');
    $rs = $db->Execute($sql);
    if ($rs === false) {
        throw new Jp7_Interadmin_Exception($db->ErrorMsg());
    }
    if ($row = $rs->FetchNextObj()) {
        $I = $row->id;
    }
    $rs->Close();

    return $I;
}

/**
 * class jp7_lang.
 *
 * @author JP
 *
 * @version (2007/08/08)
 */
class jp7_lang
{
    /**
     * Checks the current language.
     *
     * @param string $lang  Current language, the default value is "".
     * @param bool   $force If <tt>TRUE</tt> it skips the check and $lang becomes the current language, the default value is <tt>FALSE</tt>.
     *
     * @global string
     * @global string
     *
     * @return jp7_lang Object with the following properties: $this->lang, $this->prefix, $this->path and $this->path_2.
     *
     * @author JP
     *
     * @version (2006/09/12)
     */
    public function __construct($lang = '', $force = false)
    {
        global $config;
        if (!$lang) {
            $lang = $config->lang_default;
        }
        if ($force) {
            $this->lang = $lang;
        } else {
            $this->lang = ($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $_SERVER['SCRIPT_NAME'];
            if ($_SERVER['QUERY_STRING']) {
                $pos1 = strpos($this->lang, $_SERVER['QUERY_STRING']);
                if ($pos1 !== false) {
                    $this->lang = mb_substr($this->lang, 0, $pos1);
                }
            }
            $this->lang = explode('/', $this->lang);
            //if($c_path){ // Old Way
                $path_size = explode('/', jp7_path($config->server->path));
            $path_size = count($path_size);
                //$this->lang=$this->lang[$path_size]; // Old Way
                $this->lang = $this->lang[count($this->lang) - 3]; // For Hotsites
            //}else $this->lang=$this->lang[1]; // Old Way
            $this->lang = str_replace('_', '', $this->lang); // Apache Redirect
        }
        $langs = ['de', 'en', 'es', 'fr', 'jp', 'pt', 'pt-br'];
        //if(!$this->lang||$this->lang=="pt-br"||$this->lang=="site"||$this->lang==$config->name_id||$this->lang=="hotsites"||$this->lang=="_hotsites"||$this->lang=="intranet"||$this->lang=="extranet"||$this->lang=="wap"){
        if (!in_array($this->lang, $langs) || $this->lang == $config->lang_default) {
            $this->lang = $lang;
            $this->prefix = '';
            $this->path = '';
            $this->path_url = 'site/';
        } else {
            $this->prefix = '_'.$this->lang;
            $this->path = $this->lang.'/';
            $this->path_url = $this->path;
        }
        $this->path_2 = $this->path_url; // Replace later (?)
    }
    /**
     * Creates a link for the current page on another language.
     *
     * @param string $new_lang Language the link will use.
     *
     * @global string
     * @global string
     *
     * @return string Link pointing to the current page on the given language.
     *
     * @author Carlos
     *
     * @version (2008/06/26)
     */
    public function getUri($new_lang, $uri = '')
    {
        global $config;
        $newLang = new self($new_lang, true);
        if (!$uri) {
            $uri = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
        // Separates Query String from Uri
        $uri_parts = explode('?', $uri);
        if ($uri_parts[1]) {
            $uri = $uri_parts[0];
            $querystring_arr = explode('&', $uri_parts[1]);
            foreach ($querystring_arr as $value) {
                $arr = explode('=', $value);
                if ($arr[0] != 'id') {
                    $values[] = $arr[0].'='.$arr[1];
                }
            }
            if ($values) {
                $querystring = '?'.implode('&', (array) $values);
            }
        }
        // Home
        $uri_lang = jp7_path(str_replace($config->url, '', $uri));
        if ($config->url == $uri || $uri_lang == $this->path_url) {
            return $config->url.(($newLang->path_url == 'site/') ? '' : $newLang->path_url).$querystring;
        } else {
            // Default
            return str_replace($config->url.$this->path_url, $config->url.$newLang->path_url, $uri.$querystring);
        }
    }
}

/**
 * class interadmin_tipos.
 *
 * @author Thiago
 *
 * @version (2007/07/10)
 *
 * @deprecated It will be incorporated and suplanted by InterAdminTipos
 */
class interadmin_tipos
{
    /**
     * Gets data of the specified type from the database, and does the same with all of its parent types recursively.
     *
     * @param int $id_tipo ID of the type.
     *
     * @global ADOConnection
     * @global string
     * @global jp7_lang
     * @global string
     *
     * @return NULL
     */
    public function interadmin_tipos_tipos($id_tipo)
    {
        global $db, $db_prefix, $lang, $config;
        settype($id_tipo, 'integer');
        $sql = 'SELECT parent_id_tipo,model_id_tipo,nome,nome'.(($lang->lang != $config->lang_default) ? '_'.$lang->lang : '').' AS nome_lang,template,menu,busca,restrito,admin FROM '.$db_prefix.'_tipos WHERE id_tipo='.$id_tipo;
        $rs = interadmin_query($sql);
        while ($row = $rs->FetchNextObj()) {
            $this->id_tipo[] = $id_tipo;
            $this->model_id_tipo[] = $row->model_id_tipo;
            $this->nome[] = ($row->nome_lang) ? $row->nome_lang : $row->nome;
            $this->nome_original[] = $row->nome;
            $this->nome_id[] = toId($row->nome);
            $this->template[] = $row->template;
            $this->menu[] = $row->menu;
            $this->busca[] = $row->busca;
            $this->restrito[] = $row->restrito;
            $this->admin[] = $row->admin;
            $this->interadmin_tipos_tipos($row->parent_id_tipo);
        }
        $rs->Close();
    }
    /**
     * Finds the type of a record by its ID, gets its data from the database, and does the same with all of its parent types recursively.
     *
     * @param int  $id_tipo        ID of the type.
     * @param int  $id             ID of the record (optional), it overrides the value of $id_tipo with the record's id_tipo.
     * @param bool $replaceGlobals If <tt>TRUE</tt> the global $id_tipo is replaced by the local $id_tipo, the default value is <tt>FALSE</tt>.
     *
     * @global ADOConnection
     * @global string
     * @global string
     * @global string
     *
     * @todo Check if the "Parent Id" and "Grand Parent Id" code are working properly, since they are replacing $id_tipo it might not bring the children data.
     *
     * @return interadmin_tipos
     */
    public function __construct($id_tipo, $id = 0, $replaceGlobals = false)
    {
        global $db, $db_prefix, $lang, $id_nome, $implicit_parents_names;
        // Id
        if ($id && is_numeric($id)) {
            $sql = 'SELECT id_tipo,parent_id,varchar_key FROM '.$db_prefix.$lang->prefix.' WHERE id='.$id;
            $rs = $db->Execute($sql);
            if ($rs === false) {
                throw new Jp7_Interadmin_Exception($db->ErrorMsg());
            }
            while ($row = $rs->FetchNextObj()) {
                $id_tipo = $row->id_tipo;
                $parent_id = $row->parent_id;
                $id_nome = $row->varchar_key;
            }
            $rs->Close();
        }
        // Parent Id
        if ($parent_id && is_numeric($parent_id)) {
            $sql = 'SELECT id_tipo,parent_id FROM '.$db_prefix.$lang->prefix.' WHERE id='.$parent_id;
            $rs = $db->Execute($sql);
            if ($rs === false) {
                throw new Jp7_Interadmin_Exception($db->ErrorMsg());
            }
            while ($row = $rs->FetchNextObj()) {
                $id_tipo = $row->id_tipo;
                $grand_parent_id = $row->parent_id;
            }
            $rs->Close();
        }
        // Grand Parent Id
        if ($grand_parent_id && is_numeric($grand_parent_id)) {
            $sql = 'SELECT id_tipo FROM '.$db_prefix.$lang->prefix.' WHERE id='.$grand_parent_id;
            $rs = $db->Execute($sql);
            if ($rs === false) {
                throw new Jp7_Interadmin_Exception($db->ErrorMsg());
            }
            while ($row = $rs->FetchNextObj()) {
                $id_tipo = $row->id_tipo;
            }
            $rs->Close();
        }
        // Tipos
        if ($id_tipo && is_numeric($id_tipo)) {
            if ($replaceGlobals) {
                $GLOBALS['id_tipo'] = $id_tipo;
            }
            $this->interadmin_tipos_tipos($id_tipo);
            if ($this->id_tipo) {
                $this->id_tipo = array_reverse($this->id_tipo);
                $this->model_id_tipo = array_reverse($this->model_id_tipo);
                $this->nome = array_reverse($this->nome);
                $this->nome_original = array_reverse($this->nome_original);
                $this->nome_id = array_reverse($this->nome_id);
                $this->template = array_reverse($this->template);
                $this->menu = array_reverse($this->menu);
                $this->busca = array_reverse($this->busca);
                $this->restrito = array_reverse($this->restrito);
                $this->admin = array_reverse($this->admin);
                $this->i = count($this->id_tipo);
                $this->path = implode('/', $this->nome_id);
                $this->path_title = implode('/', $this->nome);
            }
        }
        $path_seo = '';
        $path_seo_arr = [];
        foreach ((array) $this->nome as $key => $nome) {
            if (!in_array($nome, (array) $implicit_parents_names)) {
                $path_seo = toSeo($nome); //. (($key < count($this->nome) - 1) ? '/' : '');
                $path_seo_arr[] = $path_seo;
                $this->path_seo[] = '/'.$GLOBALS['c_path'].implode('/', $path_seo_arr);
            } else {
                $this->path_seo[] = '/'.$GLOBALS['c_path'].toSeo($nome);
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
 *
 * @global ADOConnection
 * @global string
 * @global string
 *
 * @return int|NULL If $id is specified it returns its id_tipo, otherwise it returns the first child's id_tipo for the $parent_id_tipo. If both fail nothing is returned.
 *
 * @version (2007/05/23)
 */
function interadmin_id_tipo($id = '', $parent_id_tipo = 0, $model_id_tipo = 0)
{
    global $db;
    global $db_prefix;
    global $lang;
    if ($id) {
        $sql = 'SELECT id_tipo FROM '.$db_prefix.$lang->prefix.
        ' WHERE id='.$id;
    } else {
        $sql = 'SELECT id_tipo FROM '.$db_prefix.'_tipos'.
        ' WHERE parent_id_tipo='.$parent_id_tipo.
        (($model_id_tipo) ? ' AND model_id_tipo='.$model_id_tipo : '').
        ' ORDER BY ordem,nome';
    }
    $sql .= ' LIMIT 1';
    $rs = $db->Execute($sql);
    if ($rs === false) {
        throw new Jp7_Interadmin_Exception($db->ErrorMsg());
    }
    if ($row = $rs->FetchNextObj()) {
        return $row->id_tipo;
    }
    $rs->Close();
}

/**
 * class interadmin_cabecalho.
 *
 * @version (2006/11/29)
 */
class interadmin_cabecalho
{
    /**
     * Gets text and images of the specified type.
     *
     * @param int    $i             Index of the type on the global $tipos, the default value is 0.
     * @param int    $model_id_tipo Value of the model_id_tipo of this type, used to find the correct type, default value is 5.
     * @param string $check         Fields which will have their values checked to make sure they are not empty, names separated by comma (,), the default value is "file_1,file_2".
     * @param bool   $rand          The default value is <tt>FALSE</tt>.
     *
     * @global ADOConnection
     * @global string
     * @global string
     *
     * @return interadmin_cabecalho
     *
     * @version (2006/11/29)
     */
    public function __construct($i = 0, $model_id_tipo = 5, $check = 'file_1,file_2', $rand = false)
    {
        global $db;
        global $db_prefix;
        global $tipos;
        if ($id_tipo = interadmin_id_tipo(0, $tipos->id_tipo[$i], $model_id_tipo)) {
            $sql = 'SELECT varchar_key,varchar_1,varchar_2,file_1,file_2 FROM '.$db_prefix.$lang->prefix.
            ' WHERE id_tipo='.$id_tipo.
            " AND char_key<>''".
            " AND publish<>''".
            " AND deleted=''".
            ' ORDER BY int_key,date_publish DESC';
            $rs = $db->Execute($sql);
            if ($rs === false) {
                throw new Jp7_Interadmin_Exception($db->ErrorMsg());
            }
            if ($rand) {
                $rand = rand(1, $rs->RecordCount());
            }
            $j = 1;
            while ($row = $rs->FetchNextObj()) {
                if ($j == $rand || !$rand) {
                    $this->varchar_key = $row->varchar_key;
                    $this->varchar_1 = $row->varchar_1;
                    $this->varchar_2 = $row->varchar_2;
                    $this->varchar_3 = $row->varchar_3;
                    $this->file_1 = $row->file_1;
                    $this->file_2 = $row->file_2;
                    break;
                }
                $j++;
            }
            $rs->Close();
            $check_arr = explode(',', $check);
            foreach ($check_arr as $check_field) {
                eval("\$check_value=\$this->".$check_field.';');
                if ($check_value) {
                    $check_ok = true;
                    break;
                }
            }
        }
        if (!$check_ok && $i) {
            $this->interadmin_cabecalho($i - 1, $model_id_tipo, $check, $rand);
        }
    }
}

function jp7_flash($src, $w, $h, $alt = '', $id = '', $xtra = '', $parameters = '')
{
    return Jp7_Deprecated::jp7_flash($src, $w, $h, $alt, $id, $xtra, $parameters);
}

/**
 * Adds a trailing slash on a path, in case it doesn't have one.
 *
 * @param string $S Input String (Path, URL).
 * @param bool $reverse If <tt>TRUE</tt> the trailing slash is removed instead of added, the default value is <tt>FALSE</tt>.
 *
 * @return string String with a trailing slash.
 *
 * @version (2003/08/25)
 */
function jp7_path($S, $reverse = false)
{
    if ($reverse) {
        return (mb_substr($S, mb_strlen($S) - 1) == '/') ? mb_substr($S, 0, mb_strlen($S) - 1) : $S;
    } else {
        return (mb_substr($S, -1) == '/' || !$S) ? $S : $S.'/';
    }
}

/**
 * Attempts to find the root directory.
 *
 * @global string
 * @global bool
 * @global string
 *
 * @return string Root directory.
 *
 * @version (2009/03/20)
 */
function jp7_doc_root()
{
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?: @ini_get('doc_root');

    return jp7_path($docRoot);
}

/**
 * Attempts to include a file from two levels above and, if it fails, tries from the root.
 *
 * @param string $file Filename which will be included. e.g. "inc/example.php".
 *
 * @global Jp7_Debugger
 *
 * @return NULL
 *
 * @version (2008/06/13)
 *
 * @deprecated Instead of using this function use "include jp7_path_find('folder/filename.php');"
 */
function jp7_include($file)
{
    global $debugger;
    if ($debugger) {
        $debugger->showFilename($file);
    }
    $include = @include $file;
    if (!$include) {
        if (!@include '../../'.$file) {
            @include jp7_doc_root().$file;
        }
    }
}

/**
 * Attempts to find a file on the directories above the current directory and, if it fails, it points to the root.
 *
 * @param string $file Filename.
 *
 * @global Jp7_Debugger
 * @staticvar int $path_levels Number of paths from the root to the current folder.
 *
 * @return string Path to the file.
 *
 * @author JP, Carlos
 *
 * @version (2009/02/25)
 * @deprecated
 */
function jp7_path_find($file)
{
    global $debugger;
    static $path_levels;
    if (!$path_levels) {
        $path_levels = count(explode('/', $_SERVER['PHP_SELF'])) - 1;
    } // Total de pastas.
    static $web_root;
    if (!$web_root) {
        $web_root = dirname(dirname(__FILE__));
    }
    for ($i = 0; $i <= $path_levels; $i++) {
        ($i) ? $path .= '../' : $path = '';
        if ($ok = @file_exists($path.$file)) {
            break;
        } elseif (strpos($path.$file, $web_root) === 0) {
            break; // já na raiz, evita erros de open_base_dir()
        }
    }
    if (!$ok) {
        // Necessário para localização de includes em templates
        $path = jp7_path($GLOBALS['c_doc_root'], true).dirname($_SERVER['REQUEST_URI']).'/';
        $ok = @file_exists($path.$file);
    }
    if (!$ok) {
        if (strpos($file, '/head.php') !== false) {
            return jp7_path_find(str_replace('/head.php', '/7.head.php', $file));
        }
        if ($GLOBALS['c_template'] && strpos($file, '../../inc/') !== false) {
            return jp7_path_find(str_replace('../../inc/', '../../../_templates/'.$GLOBALS['c_template'].'/inc/', $file));
        }
        $path = '';
        if (@file_exists(jp7_doc_root().$file)) {
            $path = jp7_doc_root();
        }
    }

    return ($debugger) ? $debugger->showFilename($path.$file) : $path.$file;
}

/**
 * Gets the extension of a file.
 *
 * @param string $S Filename.
 *
 * @return string Extension of the file or "---" if no extension is found.
 *
 * @version (2003/08/25)
 */
function jp7_extension($S)
{
    if (strpos($S, '?') !== false) {
        // Tirando a Query String
        $arr = explode('?', $S);
        $S = reset($arr);
    }
    $path_parts = pathinfo($S);
    $ext = trim($path_parts['extension'].' ');

    return (!$ext) ? '---' : $ext;
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
 *
 * @see http://www.php.net/manual/en/function.mail.php
 *
 * @global bool
 *
 * @return bool Returns <tt>TRUE</tt> if the mail was successfully accepted for delivery, <tt>FALSE</tt> otherwise.
 *
 * @todo The parameter $attachments is not used.
 *
 * @author JP
 *
 * @version (2007/08/01)
 */
function jp7_mail($to, $subject, $message, $headers = '', $parameters = '', $template = '', $html = true, $attachments = '')
{
    global $debug, $config;
    // Mensagem alternativa em texto
    if (strpos($message, '<br>') !== false) {
        $text_hr = '';
        for ($i = 0; $i < 80; $i++) {
            $text_hr .= '-';
        }
        $message_text = str_replace("\r", '', $message);
        $message_text = str_replace("\n", '', $message_text);
        $message_text = str_replace('&nbsp;', ' ', $message_text);
        $message_text = str_replace('<hr size=1 color="#666666">', $text_hr."\r\n", $message_text);
        $message_text = str_replace('<br>', "\r\n", $message_text);
    }
    $message_text = strip_tags($message_text);
    // HTML
    if ($html) {
        $message_html = str_replace("\r\n", "\n", $message); // PC to Linux
        $message_html = str_replace("\r", "\n", $message_html); // Mac to Linux
        $message_html = str_replace("\n", "\r\n", $message_html); // Linux to Mail Format
        if (strpos($message_html, '<br>') === false && strpos($message, '<html>') === false) {
            $message_html = str_replace("\r\n", "<br>\r\n", $message_html); // Linux to Mail Format
        }
        if ($template) {
            @ini_set('allow_url_fopen', '1');
            if ((!dirname($template) || dirname($template) == '.') && @ini_get('allow_url_fopen')) {
                $template = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['SCRIPT_NAME']).'/'.$template;
            }
            if ($pos1 = strpos($template, '?')) {
                //$template=mb_substr($template,0,$pos1+1).urlencode(mb_substr($template,$pos1+1));
                $template = str_replace(' ', '%20', $template);
            }
            if (strpos($template, 'http://') !== 0) {
                $template = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']).'/'.$template;
            }
            //valida usuário logado e caso o template inicie em http
            if ($_SERVER['PHP_AUTH_USER']) {
                $template = str_replace('http://', 'http://'.$_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'].'@', $template);
            }
            $template = file_get_contents($template);

            //echo "template: ".$template;
            $message_html = str_replace('%MESSAGE%', $message_html, $template);
        }
        // Evitar que a quebra de linha deixe tags quebradas - Bug
        $message_html = str_replace('<br', "\r\n<br", $message_html);
        $message_html = str_replace('=', '=3D', $message_html);
        // Boundaries
        $mime_boundary_1 = '==Multipart_Boundary_x'.md5(time() + 1).'x';
        $mime_boundary_2 = '==Multipart_Boundary_x'.md5(time() + 2).'x';
        // Headers
        $headers = "MIME-Version: 1.0\r\n".
            $headers.
            "Return-Errors-To: sites@jp7.com.br\r\n".
            "Content-Type: multipart/alternative;\r\n".
            '	boundary="'.$mime_boundary_2.'"';
            //"Content-Type: multipart/mixed;\r\n".
            //"	boundary=\"".$mime_boundary_1."\"";
            // Message
        $message = "This is a multi-part message in MIME format.\r\n\r\n".
            //"--".$mime_boundary_1."\r\n".
            //"Content-Type: multipart/alternative;\r\n".
            //"	boundary=\"".$mime_boundary_2."\"\r\n\r\n".
            // TEXT
            '--'.$mime_boundary_2."\r\n".
            "Content-Type: text/plain; charset=\"UTF-8\"\r\n".
            "Content-Transfer-Encoding: quoted-printable\r\n\r\n".
            $message_text."\r\n\r\n".
            // HTML
            '--'.$mime_boundary_2."\r\n".
            "Content-Type: text/html; charset=\"UTF-8\"\r\n".
            "Content-Transfer-Encoding: quoted-printable\r\n\r\n".
            $message_html."\r\n\r\n".
            // Footer
            '--'.$mime_boundary_2."--\r\n\r\n";
    } else {
        // Headers
        $headers .=
            "Return-Errors-To: sites@jp7.com.br\r\n".
            'Content-Type: text/plain';// charset=\"UTF-8\"\r\n".
        //"Content-Transfer-Encoding: quoted-printable";
        // Message
        $message = $message_text;
    }
    // Encode
    $subject = jp7_encode_mimeheader($subject);
    // Check CRLF
    if (strpos($_ENV['OS'], 'Windows') === false || !$_ENV['OS']) {
        $message = str_replace("\r\n", "\n", $message);
        $headers = str_replace("\r\n", "\n", $headers);
    }
    // Send
    if ($config->server->type != InterSite::PRODUCAO) {
        $to = 'debug@jp7.com.br';
    }
    $mail = mail($to, $subject, $message, $headers, $parameters);
    if (!$mail) {
        $mail = mail($to, $subject, $message, $headers); // Safe Mode
    }
    if ($debug) {
        echo 'jp7_mail('.htmlentities($to).': '.$mail.'<br>';
    }

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
 * @param bol $truecolor If <tt>TRUE</tt> Os PNGs serão tratados com alpha. The default value is <tt>FALSE</tt>.
 *
 * @return NULL
 *
 * @version (2006/04/07)
 */
function jp7_image_text($filename_src, $filename_dst, $size, $angle, $x, $y, $col, $fontfile, $text, $padding = '0 0 0 0', $shadow = false, $antialiasing = '', $truecolor = false)
{
    $im = imagecreatefrompng($filename_src);
    if ($truecolor) {
        imagesavealpha($im, true);
    }
    $col_arr = explode(',', $col);
    $col_arr = imagecolorallocate($im, $col_arr[0], $col_arr[1], $col_arr[2]);
    if ($x !== 'center' && $shadow) {
        $shadow_color = explode(',', $shadow[color]);
        if (function_exists('imagecolorallocatealpha')) {
            $shadow_color = imagecolorallocatealpha($im, $shadow_color[0], $shadow_color[1], $shadow_color[2], $shadow_color[3]);
        } else {
            $shadow_color = imagecolorallocate($im, $shadow_color[0], $shadow_color[1], $shadow_color[2]);
        }
        imagettftext($im, $size, $angle, $x + $shadow[x], $y + $shadow[y], $shadow_color, $fontfile, $text);
    }
    $padding = explode(' ', $padding);
    imagettftext($im, $size, $angle, ($x === 'center' || $x === 'right' || $x === 'trim') ? $padding[3] : $x, ($y === 'center') ? 0 : $y, $antialiasing.$col_arr, $fontfile, $text);
    imagepng($im, $filename_dst);
    imagedestroy($im);
    // Center
    if ($x === 'center' || $y === 'center') {
        $im = imagecreatefrompng($filename_dst);
        if ($truecolor) {
            imagesavealpha($im, true);
        }
        $center = imagettfbbox($size, $angle, $fontfile, $text);
        if ($x === 'center') {
            $x = $center[4] + 1;
            $x = (imagesx($im) - $x - $padding[3]) / 2;
        }
        if ($y == 'center') {
            $y = $center[5] + 1;
            $y = (imagesy($im) - $y - $padding[0]) / 2;
        }
        if ($x !== 'center') {
            jp7_image_text($filename_src, $filename_dst, $size, $angle, $x, $y, $col, $fontfile, $text, '', $shadow, $antialiasing);
        }
        imagedestroy($im);
    // Right
    } elseif ($x === 'right') {
        $im = imagecreatefrompng($filename_dst);
        if ($truecolor) {
            imagesavealpha($im, true);
        }
        $right = imagettfbbox($size, $angle, $fontfile, $text);
        if ($x === 'right') {
            $x = $right[4];
            $x = (imagesx($im) - $x - $padding[1]);
        }
        if ($x !== 'right') {
            jp7_image_text($filename_src, $filename_dst, $size, $angle, $x, $y, $col, $fontfile, $text, '', $shadow, $antialiasing);
        }
        imagedestroy($im);
    // Trim
    } elseif ($x === 'trim') {
        $im = imagecreatefrompng($filename_dst);
        if ($truecolor) {
            imagesavealpha($im, true);
        }
        $x = imagettfbbox($size, $angle, $fontfile, $text);
        $x = $x[4] + 1;
        if ($x !== 'trim') {
            if ($truecolor) {
                $im2 = imagecreatetruecolor($x + $padding[1] + $padding[3], imagesy($im));
                imagealphablending($im2, false);
                imagecopy($im2, $im, 0, 0, 0, 0, $x + $padding[1] + $padding[3], imagesy($im));
                imagesavealpha($im2, true);
                imagepng($im2, $filename_dst);
                imagedestroy($im2);
            } else {
                $im2 = imagecreate($x + $padding[1] + $padding[3], imagesy($im));
                $im_bg = imagecolorsforindex($im, imagecolorat($im, 1, 1));
                $im_bg = imagecolorallocate($im2, $im_bg['red'], $im_bg['green'], $im_bg['blue']);
                imagefill($im2, 0, 0, $im_bg);
                imagecolortransparent($im2, $im_bg);
                imagecopymerge($im2, $im, 0, 0, 0, 0, $x + $padding[1] + $padding[3], imagesy($im), 100);
                imagepng($im2, $filename_dst);
                imagedestroy($im2);
            }
        }
        imagedestroy($im);
    }
}

function jp7_imageCreateFromBmp($filename)
{
    //Ouverture du fichier en mode binaire
    if (!$f1 = fopen($filename, 'rb')) {
        return false;
    }

    //1 : Chargement des entêtes FICHIER
    $file = unpack('vfile_type/Vfile_size/Vreserved/Vbitmap_offset', fread($f1, 14));
    if ($file['file_type'] != 19778) {
        return false;
    }
    //2 : Chargement des entêtes BMP
    $bmp = unpack(
        'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
        '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
        '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1, 40));
    $bmp['colors'] = pow(2, $bmp['bits_per_pixel']);
    if ($bmp['size_bitmap'] == 0) {
        $bmp['size_bitmap'] = $file['file_size'] - $file['bitmap_offset'];
    }
    $bmp['bytes_per_pixel'] = $bmp['bits_per_pixel'] / 8;
    $bmp['bytes_per_pixel2'] = ceil($bmp['bytes_per_pixel']);
    $bmp['decal'] = ($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
    $bmp['decal'] -= floor($bmp['width'] * $bmp['bytes_per_pixel'] / 4);
    $bmp['decal'] = 4 - (4 * $bmp['decal']);
    if ($bmp['decal'] == 4) {
        $bmp['decal'] = 0;
    }

    //3 : Chargement des couleurs de la palette
    $palette = [];
    if ($bmp['colors'] < 16777216) {
        $palette = unpack('V'.$bmp['colors'], fread($f1, $bmp['colors'] * 4));
    }

    //4 : Création de l'image
    $img = fread($f1, $bmp['size_bitmap']);
    $vide = chr(0);

    $res = imagecreatetruecolor($bmp['width'], $bmp['height']);
    $p = 0;
    $y = $bmp['height'] - 1;
    while ($y >= 0) {
        $x = 0;
        while ($x < $bmp['width']) {
            if ($bmp['bits_per_pixel'] == 24) {
                $color = unpack('V', mb_substr($img, $p, 3).$vide);
            } elseif ($bmp['bits_per_pixel'] == 16) {
                $color = unpack('n', mb_substr($img, $p, 2));
                $color[1] = $palette[$color[1] + 1];
            } elseif ($bmp['bits_per_pixel'] == 8) {
                $color = unpack('n', $vide.mb_substr($img, $p, 1));
                $color[1] = $palette[$color[1] + 1];
            } elseif ($bmp['bits_per_pixel'] == 4) {
                $color = unpack('n', $vide.mb_substr($img, floor($p), 1));
                if (($p * 2) % 2 == 0) {
                    $color[1] = ($color[1] >> 4);
                } else {
                    $color[1] = ($color[1] & 0x0F);
                }
                $color[1] = $palette[$color[1] + 1];
            } elseif ($bmp['bits_per_pixel'] == 1) {
                $color = unpack('n', $vide.mb_substr($img, floor($p), 1));
                if (($p * 8) % 8 == 0) {
                    $color[1] = $color[1] >> 7;
                } elseif (($p * 8) % 8 == 1) {
                    $color[1] = ($color[1] & 0x40) >> 6;
                } elseif (($p * 8) % 8 == 2) {
                    $color[1] = ($color[1] & 0x20) >> 5;
                } elseif (($p * 8) % 8 == 3) {
                    $color[1] = ($color[1] & 0x10) >> 4;
                } elseif (($p * 8) % 8 == 4) {
                    $color[1] = ($color[1] & 0x8) >> 3;
                } elseif (($p * 8) % 8 == 5) {
                    $color[1] = ($color[1] & 0x4) >> 2;
                } elseif (($p * 8) % 8 == 6) {
                    $color[1] = ($color[1] & 0x2) >> 1;
                } elseif (($p * 8) % 8 == 7) {
                    $color[1] = ($color[1] & 0x1);
                }
                $color[1] = $palette[$color[1] + 1];
            } else {
                return false;
            }
            imagesetpixel($res, $x, $y, $color[1]);
            $x++;
            $p += $bmp['bytes_per_pixel'];
        }
        $y--;
        $p += $bmp['decal'];
    }
    //Fermeture du fichier
    fclose($f1);

    return $res;
}

/**
 * Resizes an image to the specified dimensions.
 *
 * @param 	resource	$resource 	An image resource, returned by one of the image creation functions, such as imagecreatefromjpeg(). If null it is created automatically.
 * @param 	string 		$source		Path to the source image.
 * @param 	string 		$dest		Path to the destination image.
 * @param 	int 		$width 		Destination width.
 * @param 	int 		$height		Destination height.
 * @param 	int 		$quality	Ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file). The default value is 90.
 * @param 	int 		$maxsize	Maximum filesize in bytes, from this size the quality is changed to the $q value (used only if the destination dimensions are bigger). The default value is 10000000 (10MB).
 * @param 	bool|string	$crop		Boolean values TRUE, FALSE or the string 'border'. Defaults to FALSE.
 * @param 	bool		$imagemagick
 * @param 	bool		$enlarge 	Sets whether the image can be resized to a bigger resolution or not.
 *
 * @return 	string		Returns the mime type of the generated image.
 *
 * @version (2008/08/29)
 */
function jp7_resizeImage($resource, $source, $dest, $width, $height, $quality = 90, $maxsize = 10000000, $crop = false, $imagemagick = false, $enlarge = false)
{
    // Declare vars
    $dif_w = 0;
    $dif_h = 0;
    $new_width = 0;
    $new_height = 0;
    // Params can also be passed as an array of options
    if (is_array($quality)) {
        $options = $quality;
        if (isset($options['quality'])) {
            $quality = $options['quality'];
        } else {
            $quality = 90; // default value
        }
        if (isset($options['maxsize'])) {
            $maxsize = $options['maxsize'];
        }
        if (isset($options['crop'])) {
            $crop = $options['crop'];
        }
        if (isset($options['bgcolor'])) {
            $bgcolor = $options['bgcolor'];
            if (is_string($bgcolor)) {
                $bgcolor = explode(',', $bgcolor);
            }
        }
        if (isset($options['enlarge'])) {
            $enlarge = $options['enlarge'];
        }
    }
    // Check GD
    $isGdEnabled = function_exists('imagecreatefromjpeg');
    $command_path = '/usr/bin/';
    // Check Size and Orientation (Horizontal x Vertical)
    if ($isGdEnabled) {
        // GD Get Size
        $imageinfo = @getimagesize($source);
        $source_w = $imageinfo[0];
        $source_h = $imageinfo[1];
        $mime = $imageinfo['mime'];
    } else {
        // Magick Get Size
        $command = $command_path.'identify -verbose '.$source;
        exec($command, $output, $return_var);
        $source_geometry = explode('x', mb_substr($output[2], strpos($output[2], ':') + 2));
        $source_w = $source_geometry[0];
        $source_h = $source_geometry[1];
    }
    // Creating resource from source file.
    if (is_null($resource)) {
        if ($mime == 'image/gif') {
            $resource = imagecreatefromgif($source);
        } elseif ($mime == 'image/png') {
            $resource = imagecreatefrompng($source);
        } elseif ($mime == 'image/bmp') {
            $resource = jp7_imageCreateFromBmp($source);
        } else {
            $resource = imagecreatefromjpeg($source);
        }
    }
    // Source and destination with the same proportions, no complicated math required
    if ($source_w / $source_h == $width / $height) {
        $dest_w = $width;
        $dest_h = $height;
    // Destination is square (with same width and height - crop if needed)
    } elseif ($crop && $crop !== 'border') {
        $pre_dst_w = ceil(($height * $source_w) / $source_h);
        $pre_dst_h = ceil(($width * $source_h) / $source_w);
        if ($pre_dst_h > $height) {
            $dest_w = $width;
            $dest_h = $pre_dst_h;
            $dif_h = round(($height - $pre_dst_h) / 2);
        } else {
            $dest_h = $height;
            $dest_w = $pre_dst_w;
            $dif_w = round(($width - $pre_dst_w) / 2);
        }
        $new_width = $width;
        $new_height = $height;
    // The image is resized until it gets the maximum width or height (without crop)
    } else {
        $pre_dst_w = intval(round(($height * $source_w) / $source_h));
        $pre_dst_h = intval(round(($width * $source_h) / $source_w));
        if ($pre_dst_h <= $height) {
            $dest_w = $width;
            $dest_h = $pre_dst_h;
        } else {
            $dest_h = $height;
            $dest_w = $pre_dst_w;
        }
        if ($crop === 'border') {
            $new_width = $width;
            $new_height = $height;
            $dif_w = ($new_width - $dest_w) / 2;
            $dif_h = ($new_height - $dest_h) / 2;
        }
    }
    if (!$new_width) {
        $new_width = $dest_w;
    }
    if (!$new_height) {
        $new_height = $dest_h;
    }
    // Checks if destination image is bigger than source image
    if ($dest_w >= $source_w && $dest_h >= $source_h && !$enlarge) {
        // No-Resize and Check Size
        if (filesize($source) > $maxsize) {
            $im_dest = $resource;
            if ($isGdEnabled) {
                // GD Convert Quality
                if ($mime == 'image/gif') {
                    imagegif($im_dest, $dest);
                } elseif ($mime == 'image/png') {
                    imagepng($im_dest, $dest);
                } else {
                    imagejpeg($im_dest, $dest, $quality);
                }
            } else {
                // Magick Convert Quality
                $command = $command_path.'convert '.$source.' -quality '.$quality." +profile '*' ".$dest;
                exec($command, $output, $return_var);
            }
        } else {
            copy($source, $dest);
        }
    } else {
        if ($isGdEnabled) {
            // GD Resize
            $im_dest = imagecreatetruecolor($new_width, $new_height);
            if ($mime == 'image/gif' || $mime == 'image/png') {
                imagealphablending($im_dest, false);
                imagesavealpha($im_dest, true);
            }
            if ($crop == 'border') {
                if ($bgcolor) {
                    $bg = imagecolorallocate($im_dest, $bgcolor[0], $bgcolor[1], $bgcolor[2]);
                } else {
                    $bg = imagecolorat($resource, 1, 1);
                    $color = imagecolorsforindex($resource, $bg);
                    $bg = rgba2int($color['red'], $color['green'], $color['blue'], $color['alpha']);
                }
                imagefill($im_dest, 0, 0, $bg);
            }
            imagecopyresampled($im_dest, $resource, $dif_w, $dif_h, 0, 0, $dest_w, $dest_h, $source_w, $source_h);
            if ($options['borderRadius']) {
                $im_dest = jp7_imageRoundedCorner($im_dest, $options['borderRadius'], $options['borderColor']);
                imagepng($im_dest, $dest, round(($quality / 10) - 1));
            } else {
                if ($mime == 'image/gif' || $mime == 'image/png') {
                    imagepng($im_dest, $dest, round(($quality / 10) - 1));
                } else {
                    imagejpeg($im_dest, $dest, $quality);
                    $mime = 'image/jpeg'; // It´s being saved as a JPEG file
                }
            }
            imagedestroy($im_dest);
        } else {
            // Magick Resize
            $command = $command_path.'convert '.$source.' -resize '.$dest_w.'x'.$dest_h.'! -quality '.$quality." +profile '*' ".$dest;
            exec($command, $output, $return_var);
        }
    }

    return $mime;
}

/**
 * Convert RGBA to Long Int.
 *
 * @author Lucas Martins at JP7
 *
 * @param int $red
 * @param int $green
 * @param int $blue
 * @param int $alpha [optional]
 *
 * @return
 */
function rgba2int($red, $green, $blue, $alpha = 0)
{
    //return ($a << 24) + ($b << 16) + ($g << 8) + $r;
    return ($alpha << 24) + (256 * 256 * $red) + (256 * $green) + $blue;
}

/**
 * Adiciona radius na imagem.
 *
 * @param resource $im
 * @param int $radius [optional] Pixels
 * @param string $color [optional] RGB, ex.: 255,255,255
 *
 * @return resource
 */
function jp7_imageRoundedCorner($im, $radius = 20, $color = '255,255,255')
{
    $image_file = $_GET['src'];
    $corner_radius = ($radius) ? $radius : 20; // The default corner radius is set to 20px
    $angle = isset($_GET['angle']) ? $_GET['angle'] : 0; // The default angle is set to 0º
    $topleft = (isset($_GET['topleft']) and $_GET['topleft'] == 'no') ? false : true; // Top-left rounded corner is shown by default
    $bottomleft = (isset($_GET['bottomleft']) and $_GET['bottomleft'] == 'no') ? false : true; // Bottom-left rounded corner is shown by default
    $bottomright = (isset($_GET['bottomright']) and $_GET['bottomright'] == 'no') ? false : true; // Bottom-right rounded corner is shown by default
    $topright = (isset($_GET['topright']) and $_GET['topright'] == 'no') ? false : true; // Top-right rounded corner is shown by default

    $images_dir = 'images/';
    $corner_source = imagecreatefrompng('D:/Inetpub/WWWRoot/_default/img/rounded_corner.png');
    $color = explode(',', $color);
    $corner_color = imagecolorallocate($corner_source, $color[0], $color[1], $color[2]);
    imagefill($corner_source, 0, 0, $corner_color);

    $corner_width = imagesx($corner_source);
    $corner_height = imagesy($corner_source);
    $corner_resized = imagecreatetruecolor($corner_radius, $corner_radius);
    imagecopyresampled($corner_resized, $corner_source, 0, 0, 0, 0, $corner_radius, $corner_radius, $corner_width, $corner_height);

    $corner_width = imagesx($corner_resized);
    $corner_height = imagesy($corner_resized);
    $image = imagecreatetruecolor($corner_width, $corner_height);
    //$image = imagecreatefromjpeg($images_dir . $image_file); // replace filename with $_GET['src']
    $image = $im;
    $size[0] = imagesx($image); // replace filename with $_GET['src']
    $size[1] = imagesy($image); // replace filename with $_GET['src']
    $white = imagecolorallocate($image, 255, 255, 255);
    $black = imagecolorallocate($image, 0, 0, 0);
    $color_image = imagecolorallocate($image, $color[0], $color[1], $color[2]);

    // Top-left corner
    if ($topleft == true) {
        $dest_x = 0;
        $dest_y = 0;
        imagecolortransparent($corner_resized, $black);
        imagecopymerge($image, $corner_resized, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);
    }

    // Bottom-left corner
    if ($bottomleft == true) {
        $dest_x = 0;
        $dest_y = $size[1] - $corner_height;
        $rotated = imagerotate($corner_resized, 90, 0);
        imagecolortransparent($rotated, $black);
        imagecopymerge($image, $rotated, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);
    }

    // Bottom-right corner
    if ($bottomright == true) {
        $dest_x = $size[0] - $corner_width;
        $dest_y = $size[1] - $corner_height;
        $rotated = imagerotate($corner_resized, 180, 0);
        imagecolortransparent($rotated, $black);
        imagecopymerge($image, $rotated, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);
    }

    // Top-right corner
    if ($topright == true) {
        $dest_x = $size[0] - $corner_width;
        $dest_y = 0;
        $rotated = imagerotate($corner_resized, 270, 0);
        imagecolortransparent($rotated, $black);
        imagecopymerge($image, $rotated, $dest_x, $dest_y, 0, 0, $corner_width, $corner_height, 100);
    }

    //imagecolortransparent($image, $color_image);


    //imagealphablending($image, false);
    //imagesavealpha($image, true);
    $alpha = ImageColorAllocateAlpha($image, 255, 255, 255, 255);
    imagecolortransparent($image, $alpha);
    imagefill($image, 0, 0, $alpha);
    imagefill($image, $size[0] - 1, 0, $alpha);
    imagefill($image, 0, $size[1] - 1, $alpha);
    imagefill($image, $size[0] - 1, $size[1] - 1, $alpha);

    // Return
    return $image;
}

/**
 * Attempts to encode a given string by the MIME header encoding scheme.
 *
 * @param string $S The string to be encoded.
 * @param string $charset Specifies the name of the character set in which the string is represented in, the default value is "UTF-8".
 * @param string $transfer_encoding Specifies the scheme of MIME encoding. It should be either "B" (Base64) or "Q" (Quoted-Printable), the default value is "Q".
 *
 * @see http://www.php.net/manual/en/function.mb-encode-mimeheader.php
 *
 * @return string If mb_encode_mimeheader() exists it returns the converted version of the string represented in ASCII, otherwise it returns the input string.
 *
 * @version (2005/12/08)
 */
function jp7_encode_mimeheader($S, $charset = 'UTF-8', $transfer_encoding = 'Q')
{
    return (function_exists('mb_encode_mimeheader')) ? mb_encode_mimeheader($S, $charset, $transfer_encoding, (strpos($_ENV['OS'], 'Windows') === false || !$_ENV['OS']) ? "\n" : "\r\n") : $S;
}

function jp7_index($lang = '')
{
    Jp7_Deprecated::jp7_index($lang);
}

/**
 * Checks if one of the specified hosts is the current host.
 *
 * @param mixed $hosts List of hosts as array or as a string separated by comma (,).
 *
 * @return bool Returns <tt>TRUE</tt> if the current host is found.
 *
 * @author JP
 *
 * @version (2008/07/22)
 */
function jp7_host($hosts)
{
    if (!is_array($hosts)) {
        $hosts = explode(',', $hosts);
    }
    foreach ($hosts as $host) {
        if (strpos($_SERVER['HTTP_HOST'], $host) !== false) {
            return true;
            exit;
        }
    }
}

/**
 * Checks if its c_jp7 to return the filename.
 *
 * @param string $filename Input string.
 *
 * @return string If the global variable "c_jpj" is evaluated as <tt>TRUE</tt> it returns the input string, otherwise it returns an empty string.
 *
 * @deprecated jp7_path_find() has replaced this function when it comes to show filenames for debugging
 */
function getFileName($filename)
{
    return ($GLOBALS['c_jp7']) ? $filename : '';
}

/**
 * Gets file size.
 *
 * @param string $file Path to the file.
 *
 * @return string Size of the file in KB or MB.
 */
function jp7_file_size($file)
{
    return jp7_human_size(@filesize($file));
}

function jp7_human_size($size)
{
    $size = ceil($size / 1024);
    $size = ($size < 1024) ? ceil($size).'KB' : round($size / 1024, 1).'MB';

    return $size;
}

/**
 * Gets and formats the backtrace of an error, optionally sends it on an e-mail and shows user friendly maintenance screen.
 *
 * @param string 	$msgErro 	Error message, the default is <tt>NULL</tt>.
 * @param string 	$sql 		SQL it tried to execute, the default is <tt>NULL</tt>.
 * @param array 	$traceArr 	Debugging data, like the return of debug_backtrace().
 *
 * @global Jp7_Debugger
 *
 * @return string 	HTML formatted backtrace.
 * @deprecated
 */
function jp7_debug($msgErro = null, $sql = null, $traceArr = null)
{
    global $debugger, $config, $c_jp7;

    // Lançando exceção, utilizado no Web Services, por exemplo
    if ($debugger->isExceptionsEnabled()) {
        $exception = new Jp7_Interadmin_Exception($msgErro);
        $exception->setSql($sql);
        throw $exception;
    }
    if (!$traceArr) {
        $traceArr = debug_backtrace();
    }
    $backtrace = $debugger->getBacktrace($msgErro, $sql, $traceArr);

    if (!$c_jp7) {
        //Envia email e exibe tela de manutenção
        if ($config->server->type == InterSite::PRODUCAO || (!$config->server->type && strpos($_SERVER['HTTP_HOST'], '.') !== false)) {
            Jp7_View::logError();
            $debugger->sendTraceByEmail($backtrace);
            $backtrace = 'Ocorreu um erro ao tentar acessar esta página, se o erro persistir envie um email para '.
                '<a href="'.Jp7_Debugger::EMAIL.'">'.Jp7_Debugger::EMAIL.'</a>';

            $maintenanceHref = $debugger->getMaintenancePage().'?page='.$_SERVER['REQUEST_URI'].'&msg='.jp7_encrypt($msgErro, 'cryptK31');
            header('Location: '.$maintenanceHref);
            //Caso nao funcione o header, tenta por javascript
            ?>
	        <script language="javascript" type="text/javascript">
			document.location.href = "<?php echo $maintenanceHref;
            ?>";
			</script>
	        <?php
            exit;
        }
    }
    error_log($msgErro."\nURL: ".$_SERVER['REQUEST_URI']); // Usado para debug local
    return $backtrace; // Usado no die(jp7_debug()) que exibe o erro
}

/**
 * XOR Encrypts a given string with a given key phrase.
 *
 * @param string $InputString Input string
 * @param string $KeyPhrase Key phrase
 *
 * @return string Encrypted string
 */
function XOREncryption($InputString, $KeyPhrase)
{
    $KeyPhraseLength = mb_strlen($KeyPhrase);
    for ($i = 0; $i < mb_strlen($InputString); $i++) {   // Loop trough input string
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
 *
 * @return string Encrypted string
 */
function XOREncrypt($InputString, $KeyPhrase)
{
    $InputString = XOREncryption($InputString, $KeyPhrase);
    $InputString = urlencode($InputString);

    return $InputString;
}

/**
 * Decrypts a given string with a given key phrase using XOR.
 *
 * @param string $InputString Input string
 * @param string $KeyPhrase Key phrase
 *
 * @return string Decrypted string
 */
function XORDecrypt($InputString, $KeyPhrase)
{
    $InputString = urldecode($InputString);
    $InputString = XOREncryption($InputString, $KeyPhrase);

    return $InputString;
}

function moveFiles($from_path, $to_path)
{
    return Jp7_Deprecated::moveFiles($from_path, $to_path);
}

/**
 * Splits the string into an array. The difference from explode() is that jp7_explode() unsets empty values.
 *
 * @param string $separator
 * @param string $string
 * @param bool $useTrim If set the function will trim() each part of the string. Defaults to <tt>TRUE</tt>.
 *
 * @return array Array of parts withuot any empty value.
 */
function jp7_explode($separator, $string, $useTrim = true)
{
    $array = explode($separator, $string);
    if ($useTrim) {
        return array_filter($array, 'trim');
    } else {
        return array_filter($array, 'boolval');
    }
}

/**
 * Joins the array into a string. The difference from implode() is that jp7_implode() discards empty values.
 *
 * @param string $separator
 * @param string $string
 * @param bool $useTrim If set the function will trim() each part of the string. Defaults to <tt>TRUE</tt>.
 *
 * @return string
 */
function jp7_implode($separator, $array, $useTrim = true)
{
    if ($useTrim) {
        $array = array_filter($array, 'trim');
    } else {
        return array_filter($array, 'boolval');
    }

    return implode($separator, $array);
}

/**
 * Same as file_exists, the difference is that it takes include_path in consideration.
 *
 * @param string $filename Relative path to the file or directory.
 *
 * @return bool Returns TRUE if the file or directory specified by filename exists on any of the directories listed on include_path; FALSE otherwise.
 */
function jp7_file_exists($filename)
{
    $include_paths = explode(PATH_SEPARATOR, get_include_path());
    foreach ($include_paths as $include_path) {
        if (file_exists($include_path.'/'.$filename)) {
            return true;
        }
    }

    return false;
}

function jp7_is_windows()
{
    return mb_strtoupper(mb_substr(PHP_OS, 0, 3)) === 'WIN';
}

/**
 * Checks if an executable program exists. On Windows it works only for .exe files.
 * Searchs for the executable file inside the directories on the %PATH% variable.
 *
 * @param $executable Name of the file without the extension (.exe), e.g. "svn".
 *
 * @return bool
 */
function jp7_is_executable($executable)
{
    if (JP7_IS_WINDOWS) {
        $comando = 'for %G in ("%path:;=" "%") do @IF EXIST %G/'.$executable.'.exe echo 1';
    } else {
        $comando = 'type -P '.$executable;
    }

    return (bool) @shell_exec($comando);
}

/**
 * Checks the current version of a package using a call to SVN executable.
 * The version is cached on a file called: $packageDir/.version.
 *
 * @param string $packageDir Name of the package on SVN repository, defaults to 'interadmin'.
 * @param string $format Format of the output. Defaults to "Versão {release} (Build {build})".
 *
 * @return string Formatted string.
 */
function interadmin_get_version()
{
    return trim(file_get_contents(BASE_PATH.'/.version'));
}

/**
 * DEPRECATED: Encodes an array as XML. Similar to json_encode().
 *
 * @param array $data
 * @param array $options
 *
 * @return string XML string.
 *
 * @deprecated Use json_encode whenever possible
 */
function jp7_xml_encode($data, $options = [])
{
    $default = [
        'send_headers' => true,
        'encoding' => 'UTF-8',
        'xml_tag' => true,
    ];

    $options += $default;
    if ($options['send_headers']) {
        header('Content-Type: text/xml; charset='.$options['encoding']);
    }
    $xml = '';
    if ($options['xml_tag']) {
        $xml .= '<?xml version="1.0" encoding="'.$options['encoding'].'"?>';
    }
    foreach ($data as $key => $value) {
        $xml .= '<'.$key.'>';
        if (is_array($value)) {
            $xml .= jp7_xml_encode($value, ['xml_tag' => false, 'send_headers' => false]);
        } else {
            $xml .= $value;
        }
        $xml .= '</'.$key.'>';
    }

    return $xml;
}

/**
 * Handles relative paths ignoring symlinks. Used on Jp7 Express.
 * Let's assume "dir/" is a symlink do "/usr/share/dir".
 * Without this function "dir/../interadmin" will be relative to the symlink directory,
 * so it is resolved to "/usr/share/interadmin".
 * This function removes "../", leaving "dir/interadmin" that is not relative to the symlink.
 *
 * @param string $path
 *
 * @return string
 */
function jp7_absolute_path($path)
{
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = [];
    foreach ($parts as $part) {
        if ('.' == $part) {
            continue;
        }
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    $path = implode(DIRECTORY_SEPARATOR, $absolutes);

    return ((strpos($absolutes[0], ':') === false) ? DIRECTORY_SEPARATOR : '').$path;
}

/**
 * Formats a DSN from an object with 'type', 'host', 'user', 'pass' and 'name'.
 *
 * @param object $db	Object with the database information.
 *
 * @return string DSN
 */
function jp7_formatDsn($db)
{
    $driver = ($db->type === 'mysql') ? 'mysqli' : $db->type;
    $dsn = $driver.'://'.$db->user.':'.$db->pass.'@'.$db->host.'/'.$db->name;
    if ($db->flags) {
        $dsn .= $db->flags;
    }

    return $dsn;
}


function jp7_get_object_vars($object)
{
    // Por estar fora de escopo do objeto enviará somente os valores visíveis
    return get_object_vars($object);
}

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @param mixed $data Value to check to see if was serialized.
 *
 * @return bool False if not serialized and true if it was.
 */
function jp7_is_serialized($data)
{
    // if it isn't a string, it isn't serialized
    if (!is_string($data)) {
        return false;
    }
    if ('N;' == $data) {
        return true;
    }
    $length = mb_strlen($data);
    if ($length < 4) {
        return false;
    }
    if (':' !== $data[1]) {
        return false;
    }
    $lastc = $data[$length - 1];
    if (';' !== $lastc && '}' !== $lastc) {
        return false;
    }
    $token = $data[0];
    switch ($token) {
        case 's':
        case 'a':
        case 'O':
        case 'b':
        case 'i':
        case 'd':
            return true;
    }

    return false;
}
/**
 * Similar to preg_quote, but for using in the replacement parameter.
 *
 * @param string $str
 *
 * @return string
 */
function preg_replacement_quote($str)
{
    return addcslashes($str, '$\\');
}
function array_full_diff($array_a, $array_b)
{
    return array_diff(array_merge($array_a, $array_b), array_intersect($array_a, $array_b));
}

function jp7_normalize($string)
{
    $table = [
        'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a', 'ª' => 'a',
        'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', '&' => 'e',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
        'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o', 'º' => 'o',
        'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
        'ç' => 'c',
        'Ç' => 'C',
        'ñ' => 'n',
        'Ñ' => 'N',
    ];

    return strtr($string, $table);
}
/**
 * Moves $key1 after $key2 if $pos = 1. Moves $key1 before $key2 if $pos = 0.
 *
 * @param array $array
 * @param string $key1
 * @param string $key2
 * @param int $pos [optional]
 *
 * @return array
 */
function array_move_key($array, $key1, $key2, $pos = 1)
{
    $value_key1 = $array[$key1];
    unset($array[$key1]);

    $keys = array_keys($array);
    $values = array_values($array);

    $pos_key2 = array_search($key2, $keys);
    array_splice($keys, $pos_key2 + $pos, 0, [$key1]);
    array_splice($values, $pos_key2 + $pos, 0, [$value_key1]);

    return array_combine($keys, $values);
}

/*
 * Removes value from array.
 *
 * @param array $array
 * @param mixed $value
 */
if (!function_exists('array_delete')) {
    function array_delete(&$array, $value)
    {
        $key = array_search($value, $array);
        if ($key !== false) {
            unset($array[$key]);
        }
    }
}

function safe_urlencode($txt)
{
    // Skip all URL reserved characters plus dot, dash, underscore and tilde..
    $pattern = "/[^-\._~:\/\?#\\[\\]@!\$&'\(\)\*\+,;=]+/";
    $result = preg_replace_callback($pattern, function ($match) {
        // ..and encode the rest!
        return rawurlencode($match[0]);
    }, $txt);
    return $result;
}

function curl_exec_follow($ch, /*int*/ $maxredirect = null)
{
    $mr = $maxredirect === null ? 5 : intval($maxredirect);

    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    if ($mr > 0) {
        $newurl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        $rch = curl_copy_handle($ch);
        curl_setopt($rch, CURLOPT_HEADER, true);
        curl_setopt($rch, CURLOPT_NOBODY, true);
        curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
        curl_setopt($rch, CURLOPT_RETURNTRANSFER, true);
        do {
            curl_setopt($rch, CURLOPT_URL, $newurl);
            $header = curl_exec($rch);
            if (curl_errno($rch)) {
                $code = 0;
            } else {
                $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                if ($code == 301 || $code == 302) {
                    preg_match('/Location:(.*?)\n/', $header, $matches);
                    $newurl = safe_urlencode(trim(array_pop($matches)));
                } else {
                    $code = 0;
                }
            }
        } while ($code && --$mr);
        curl_close($rch);
        if (!$mr) {
            if ($maxredirect === null) {
                trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
            } else {
                $maxredirect = 0;
            }

            return false;
        }
        curl_setopt($ch, CURLOPT_URL, $newurl);
    }

    return curl_exec($ch);
}

function curl_get_contents($url, $options = [])
{
    $ch = curl_init();
    $timeout = 15;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    if ($options['header']) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $options['header']);
    }
    $response = curl_exec_follow($ch, 20);

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);

    if (strpos($header, 'HTTP/1.1 200 OK') === false) {
        throw new Exception('Could not get "'.$url.'" - '.explode("\n", $header)[0]);
    }
    curl_close($ch);

    return $body;
}

function utf8_encode_recursive($array)
{
    foreach ($array as &$item) {
        if (is_string($item)) {
            $item = utf8_encode($item);
        } elseif (is_object($item) || is_array($item)) {
            $item = utf8_encode_recursive($item);
        }
    }

    return $array;
}

function utf8_decode_recursive($array)
{
    foreach ($array as &$item) {
        if (is_string($item)) {
            $item = utf8_decode($item);
        } elseif (is_object($item) || is_array($item)) {
            $item = utf8_decode_recursive($item);
        }
    }

    return $array;
}

if (!function_exists('http_parse_headers')) {
    function http_parse_headers($raw_headers)
    {
        $headers = [];
        $key = '';

        foreach (explode("\n", $raw_headers) as $i => $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], [trim($h[1])]);
                } else {
                    $headers[$h[0]] = array_merge([$headers[$h[0]]], [trim($h[1])]);
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t".trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
            }
        }

        return $headers;
    }
}

function jp7_package_path($package)
{
    $pattern = '/^(inc|classes)/';
    if (!preg_match($pattern, $package)) {
        throw new InvalidArgumentException('Package does no match '.$pattern);
    }
    return __DIR__ . '/../' . $package;
}

/**
 * Checks for Fatal Error preventing White Screen of Death.
 */
function jp7_check_shutdown()
{
    $error = error_get_last();
    switch ($error['type']) {  // Is it a Fatal Error?
        case E_ERROR:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_PARSE:
            throw new RuntimeException($error['message'].' at '.$error['file'].':'.$error['line']);
            break;
    }
}

/**
 * Checks for uncaught exceptions, preventing White Screen of Death.
 */
function jp7_check_exception($e)
{
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->handleException($e);
    return;
}
