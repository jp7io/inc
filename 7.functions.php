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
if (!function_exists('toId')) {
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
function jp7_encrypt($S, $key = '', $cipher = 'AES-256-CBC', $options = false)
{
    if (!$key) {
        $key = 9415616219865148;
    }
    $iv = 1986514894156162;
    return openssl_encrypt($S, $cipher, $key, $options, $iv);
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
function jp7_decrypt($S, $key = '', $cipher = 'AES-256-CBC', $options = false)
{
    if (!$key) {
        $key = 9415616219865148;
    }
    $iv = 1986514894156162;
    return openssl_decrypt($S, $cipher, $key, $options, $iv);
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
    return strpos($_SERVER['HTTP_REFERER'], $S) === 0 ||
        strpos($_SERVER['HTTP_REFERER'], $S_parent) === 0 ||
        strpos($_SERVER['HTTP_REFERER'], replace_prefix($protocol.'://', 'https://', $S)) === 0 ||
        strpos($_SERVER['HTTP_REFERER'], replace_prefix($protocol.'://', 'https://', $S_parent)) === 0;
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
        'Y' => $date[0],
        'm' => $date[1],
        'M' => jp7_date_month($date[1], true),
        'F' => jp7_date_month($date[1]),
        'd' => $date[2],
        'H' => $date[3],
        'i' => $date[4],
        's' => $date[5],
        'y' => mb_substr($date[0], 2),
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
    } elseif ($date instanceof \DateTimeInterface) {
        // Plain Carbon (interadmin, post classes-deprecated): Jp7_Date masked the empty/zero
        // date to '0000-00-00...', whereas Carbon underflows it to year -0001 -- normalise it
        // back to the InterAdmin zero-date convention so the split below renders empty.
        $date = ((int) $date->format('Y') < 1) ? '0000-00-00 00:00:00' : $date->format('Y-m-d H:i:s');
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
            $S .= isset($date[$x]) ? $date[$x] : $x;
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
    if (isset($M[$m - 1])) {
        $return = $M[$m - 1];
    } else {
        $return = '';
    }
    return ($sigla) ? mb_substr($return, 0, 3) : $return;
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
    $A = [];
    $campos_parameters = ['tipo', 'nome', 'ajuda', 'tamanho', 'obrigatorio', 'separador', 'xtra', 'lista', 'orderby', 'combo', 'readonly', 'form', 'label', 'permissoes', 'default', 'nome_id'];
    $campos = explode('{;}', $campos);
    for ($i = 0; $i < count($campos); $i++) {
        $parameters = explode('{,}', $campos[$i]);
        if ($parameters[0]) {
            $A[$parameters[0]]['ordem'] = ($i + 1);
            for ($j = 0; $j < count($parameters); $j++) {
                $A[$parameters[0]][$campos_parameters[$j]] = $parameters[$j];
            }
        }
    }

    return $A;
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
        global $lang;
        $sql = 'SELECT nome,nome'.$lang->prefix.' AS nome_lang FROM '.DB::getTablePrefix().'tipos WHERE id_tipo='.$id_tipo;
        $rs = DB::select($sql);
        $row = $rs[0];
        $nome = ($row->nome_lang && !$nolang) ? $row->nome_lang : $row->nome;

        return $nome;
    } else {
        return 'Tipos';
    }
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
 * @deprecated
 */
function jp7_id_value($field_value, $id_tipo = 0, $field_name = 'varchar_key')
{
    $tipoObj = new InterAdminTipo($id_tipo);
    $record = $tipoObj->records()->where($field_name, $field_value)->first();
    return $record->id ?? null;
}

/**
 * class jp7_lang.
 *
 * @author JP
 *
 * @version (2007/08/08)
 * @deprecated Use only with legacy systems
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
}

/**
 * Gets the id_tipo from the record's ID or from its parent_id_tipo.
 *
 * @param int $id Record's ID.
 * @param int $parent_id_tipo Parent type's ID (optional).
 * @param int $model_id_tipo Model type's ID (optional).
 *
 * @global string
 * @global string
 *
 * @return int|NULL If $id is specified it returns its id_tipo, otherwise it returns the first child's id_tipo for the $parent_id_tipo. If both fail nothing is returned.
 *
 * @version (2007/05/23)
 */
function interadmin_id_tipo($id = '', $parent_id_tipo = 0, $model_id_tipo = 0)
{
    global $db_prefix;
    global $lang;

    // Laravel, not the ADOdb `global $db`: this was the last live ADOdb caller in the
    // package, and the host app's bridge could not be removed while it stood. The ids were
    // interpolated straight into the SQL before; they are bound now.
    if ($id) {
        $sql = 'SELECT id_tipo FROM '.$db_prefix.$lang->prefix.' WHERE id = ?';
        $bindings = [$id];
    } else {
        $sql = 'SELECT id_tipo FROM '.$db_prefix.'_tipos'.
        ' WHERE parent_id_tipo = ?'.
        (($model_id_tipo) ? ' AND model_id_tipo = ?' : '').
        ' ORDER BY ordem,nome';
        $bindings = ($model_id_tipo) ? [$parent_id_tipo, $model_id_tipo] : [$parent_id_tipo];
    }
    $sql .= ' LIMIT 1';

    // A failed query threw Jp7_Interadmin_Exception off ErrorMsg(); Laravel raises
    // QueryException by itself, so the check is gone rather than reimplemented.
    $row = \Illuminate\Support\Facades\DB::selectOne($sql, $bindings);

    // Returns nothing (null) when there is no match, as before -- callers test falsiness.
    return $row ? $row->id_tipo : null;
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
 * Gets and formats the backtrace of an error, optionally sends it on an e-mail and shows user friendly maintenance screen.
 *
 * @param string 	$msgErro 	Error message, the default is <tt>NULL</tt>.
 * @param string 	$sql 		SQL it tried to execute, the default is <tt>NULL</tt>.
 * @param array 	$traceArr 	Debugging data, like the return of debug_backtrace().
 *
 * @global Jp7_Debugger
 *
 * @return string 	HTML formatted backtrace.
 * @deprecated Throw exception instead
 */
function jp7_debug($msgErro = null, $sql = null, $traceArr = null)
{
    throw new Jp7_Interadmin_Exception($msgErro . ($sql ? ' - SQL : ' . $sql : ''));
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

