<?
/**
 * JP7's PHP Functions 
 * 
 * Contains the main custom functions and classes
 * @author JP7 (last update by Carlos)
 * @copyright Copyright 2002-2008 JP7 (http://jp7.com.br)
 * @version 1.09 (2008/05/08)
 * @package JP7_Core
 */

/**
 * Config 
 */
if($REMOTE_ADDR=="201.6.156.39"||$LOCAL_ADDR="192.168.0.2")error_reporting(E_ALL ^ E_NOTICE);
else error_reporting(0);
if(!@ini_get("allow_url_fopen"))@ini_set("allow_url_fopen","1");

/**
 * Basic functions
 * @category Basics
 */

/**
 * Takes off diacritics and empty spaces from a string, if $tofile is <tt>FALSE</tt> (default) the case is changed to lowercase.
 *
 * @param string $S String to be formatted.
 * @param bool $tofile Sets whether it will be used for a filename or not, <tt>FALSE</tt> is the default value.
 * @param string $separador	Separator used to replace empty spaces.
 * @return string Formatted string.
 * @version (2006/01/18)
 */
function toId($S,$tofile=false,$separador=""){
	if($separador)$S=str_replace(" ",$separador,$S);
	$S=preg_replace("([·‡„‚‰¡¿√¬ƒ™])","a",$S);
	$S=preg_replace("([ÈËÍÎ…» À&])","e",$S);
	$S=preg_replace("([ÌÏÓÔÕÃŒœ])","i",$S);
	$S=preg_replace("([ÛÚıÙˆ”“’‘÷∫])","o",$S);
	$S=preg_replace("([˙˘˚¸⁄Ÿ€‹])","u",$S);
	$S=preg_replace("([Á«])","c",$S);
	$S=preg_replace("([Ò—])","n",$S);
	if($tofile){
		$S=preg_replace("([^(\d\w)])","_",$S);
	}else{
		$S=preg_replace("([^(\d\w)])",$separador,$S);
		$S=strtolower($S);
	}
	$S=preg_replace("([\(\)])","",$S);
	if($separador!="-")$S=preg_replace("([/-])","_",$S);
	return $S;
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
	if(!$html)$S=str_replace("$","$$",$S);
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
 * @global Browser $is
 */
$is=new Browser($HTTP_USER_AGENT);

/**
 * class Browser
 *
 * @version (2005/11/18)
 * @subpackage Browser
 */
class Browser{
	/**
	 * Checks browser, browser version, and whether it's a robot or not
	 *
	 * @param string $useragent Browser information from $HTTP_USER_AGENT.
	 * @return Browser
	 */	
	function Browser($useragent){
		$this->userAgent=$useragent;
		$i=0;
		if(strpos($useragent,"Safari")){
			$this->browser="sa";
			$this->v=5;
		}elseif(strpos($useragent,"Opera")){
			$this->browser="op";
			$i=strpos($useragent,"Opera")+6;
		}elseif(strpos($useragent,"MSIE")){
			$this->browser="ie";
			$i=strpos($useragent,"MSIE")+4;
		}elseif(strpos($useragent,"Mozilla/")!==false&&strpos($useragent,"compatible")===false){
			$this->browser="ns";
			$i=strpos($useragent,"Mozilla/")+8;
		}elseif(strpos($useragent,"Mozilla/5.0")!==false){
			$this->browser="mo";
			$this->v=5;
		}else{
			$this->browser=$useragent;
			$this->v=-1;
		}
		$this->sa=($this->browser=="sa");
		$this->op=($this->browser=="op");
		$this->ie=($this->browser=="ie");
		$this->ns=($this->browser=="ns");
		$this->mo=($this->browser=="mo");
		$version = "";
		while(!$this->v){
			$c=substr($useragent,$i++,1);
			if(is_numeric($c)||$c=="."||$c==" ")$version.="$c";
			else $this->v=($version)?doubleval($version):-1;
		}
		$this->ns4=($this->ns&&$version<5);
		if(strpos($useragent,"Win"))$this->os="win";
		elseif(strpos($useragent,"Mac"))$this->os="mac";
		elseif(strpos($useragent,"Unix"))$this->os="unx";
		elseif(strpos($useragent,"Linux"))$this->os="lnx";
		elseif(strpos($useragent,"SunOS"))$this->os="sol";
		else $this->os=null;
		$this->win=($this->os=="win");
		$this->mac=($this->os=="mac");
		$this->unx=($this->os=="unx");
		$this->lnx=($this->os=="lnx");
		$this->sol=($this->os=="sol");
		// Robots	
		if($this->browser==$useragent){
			$robots=array(
				"wget",
				"getright",
				"yahoo",
				"altavista",
				"lycos",
				"infoseek",
				"lwp",
				"webcrawler",
				"linkexchange",
				"slurp",
				"google"
			);
			for($i=0;$i<count($robots);$i++){
				if(strpos(strtolower($useragent),$robots[$i])!==false){
					$this->robot=$robots[$i];
					$this->browser="robot";
					break;
				}
			}
		}
	}
}

/**
 * Quotes a string to be sent to the database. Ex.: 'mysql' becomes ''mysql''.
 *
 * @param string $S The input string.
 * @global ADOConnection
 * @return string Quoted string.
 * @version (2003/08/25)
 */
function toBase($S){
	global $db;
	if($S){
		$S=$db->qstr($S,get_magic_quotes_gpc());
		$S=trim($S);
	}else{
		$S="''";
	}
	return $S;
}

/**
 * Replaces double and single quotes so they can be used inside an HTML element's attribute. Ex.: \'test\' becomes &#39;test&#39;
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
 * @return string Formatted string.
 * @version (2004/06/14)
 */
function toHTML($S,$HTML=false,$busca_replace=false){
	global $busca_varchar;
	global $busca_text;
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
 * @author update by Carlos 
 * @version (2008/05/19) - changed from $HTTP_HOST to $_SERVER['HTTP_HOST'].
 */
function checkReferer($S, $protocol="http"){
	/*
	while(strpos($S,"../")!==false){
	}
	*/
	if(!dirname($S)||dirname($S)=="."){
		$S = $protocol."://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/" . $S;
	}
	return (strpos($_SERVER['HTTP_REFERER'],$S) === 0);
}

/**
 * Shrinks the input string and adds "..." if it is larger than the maximum length, the input string is not changed if its shorter.
 *
 * @param string $S Input string.
 * @param int $size Max. lenght of the output string.
 * @return string Shrunk string.
 * @version (2004/02/28)
 * @todo Check whether the lines 365-369 are required or not.
 */
function jp7_string_left($S, $length){
	global $s_interadmin_lang;
	global $c_lang;
	if ($c_lang){
		foreach($c_lang as $item){
			if ($item[0] == $s_interadmin_lang && $item[2]) $length = $length * 8;
		}
	}
	return (strlen($S) > $length) ? substr($S, 0, $length) . "..." : $S;
}

/**
 * Sets global variables using values from superglobals if "register_globals" is OFF, emulating this feature.
 *
 * @global string
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
 * Shrinks down the input string and adds "..." if it is larger than the maximum length, or returns it unchanged if its shorter.
 *
 * @param string $length Length of the created password, the default value is 6.
 * @return string Shrunk string.
 * @version (2006/09/21)
 * @author JP
 */
function jp7_password($length=6){
	$chars="abcdefghijkmnopqrstuvwxyz023456789";
	$S="";
	for($i=0;$i<$length;$i++){
		$S.=substr($chars,rand(1,strlen($chars)),1);
	}
	return $S;
}

// 2008/02/06 by JP
function jp7_print_r($S,$return=false){
	$S="<pre>".print_r($S,1)."</pre>";
	if($return)return $S;
	else echo $S;
}


// Date

// jp7_date_split (2004/03/04)
function jp7_date_split($date){
	$date=str_replace(" ",",",$date);
	$date=str_replace("/",",",$date);
	$date=str_replace("-",",",$date);
	$date=str_replace(":",",",$date);
	$date=split(",",$date);
	return array(
		Y=>$date[0],
		m=>$date[1],
		M=>jp7_date_month($date[1]),
		d=>$date[2],
		H=>$date[3],
		i=>$date[4],
		s=>$date[5],
		y=>substr($date[0],2)
	);
}

// jp7_date_format (2006/08/24)
function jp7_date_format($date,$format="d/m/Y"){
	if($date){
		global $lang;
		if($lang->lang=="en"){
			$format=str_replace("d/m","m/d",$format);
			$format=str_replace("d-m","m-d",$format);
		}
		$date=jp7_date_split($date);
		$S="";
		for($i=0;$i<strlen($format);$i++){
			$x=substr($format,$i,1); 
			$S.=($date[$x])?$date[$x]:$x;
		}
		return $S;
	}
}

// jp7_date_week (2006/04/27)
function jp7_date_week($w,$sigla=false){
	global $lang;
	switch($lang->lang){
		case "en":$W=array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");break;
		default:$W=array("Domingo","Segunda","TerÁa","Quarta","Quinta","Sexta","S·bado");break;
	}
	if(!is_int($w))$w=date("w",strtotime($w));
	$return=$W[$w];
	return ($sigla)?substr($return,0,3):$return;
}

// jp7_date_month (2004/06/14)
function jp7_date_month($m,$sigla=false){
	global $lang;
	switch($lang->lang){
		case "en":$M=array("January","February","March","April","May","June","July","August","September","October","November","December");break;
		default:$M=array("Janeiro","Fevereiro","MarÁo","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");break;
	}
	$return=$M[$m-1];
	return ($sigla)?substr($return,0,3):$return;
}

// jp7_date_diff (2008/04/15) by Paulo
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

// Parse Data

// jp7_tel_split (2004/08/12)
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


// Database

// jp7_db_select (2006/08/23)
function jp7_db_select($table,$table_id_name,$table_id_value,$var_prefix=""){
	global $db;
	global $db_name;
	global $jp7_app;
	$sql="SELECT * FROM ".$table." WHERE ".$table_id_name."=".$table_id_value;
	$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
	while($row=$rs->FetchNextObj()){
		$meta_cols=$db->MetaColumns($table, false);
		foreach ($meta_cols as $meta){
			$name=$meta->name;
			// Dates
			if(strpos($meta->type,"date")!==false){
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

// jp7_db_insert (2007/12/17 by JP e Cristiano)
function jp7_db_insert($table,$table_id_name,$table_id_value=0,$var_prefix="",$var_check=true){
	global $db;
	global $db_name;
	
	$table_columns=$db->MetaColumnNames($table);
	array_shift($table_columns);
	$table_columns_num=count($table_columns);
	if($table_id_value){
		// Update
		$sql="UPDATE ".$table." SET ";
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
					$sql.=((!$j)?" ":",")."".$table_field_name."=".toBase($table_field_value);
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
				$valores.=toBase($table_field_value).(($i==$table_columns_num)?")":",\n");
			//se n„o for definido valor e for mysql salva branco
			}elseif(($table_field_value==="" || is_null($table_field_value)) && ($GLOBALS['db_type']==""||$GLOBALS['db_type']=="mysql")){
				$valores.="''".(($i==$table_columns_num)?")":",\n");
			//se n„o for definido valor e for != de mysql
			}else{
				$valores.="NULL".(($i==$table_columns_num)?")":",\n");
			}
			$i++;
		}
		$sql="INSERT INTO ".$table." (".$sql_campos."VALUES (".$valores;
		$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(), $sql));
		// Last ID
		eval("global \$".$var_prefix.$table_id_name.";");
		eval("\$".$var_prefix.$table_id_name."=".$db->Insert_ID().";");
		return $db->Insert_ID();
	}
}

// 2007/02/22 by JP
class jp7_db_pages{
	function jp7_db_pages($sql=null,$limit=10,$page=1,$type="",$numbers_limit="1000",$parameters="",$separador="|",$go_char="&gt;",$back_char="&lt;",$go_char_plus="&raquo;",$back_char_plus="&laquo;",$records=null){
		// SQL
		global $db;
		global $db_name;
		global $rs;
		if(!$page)$page=1;
		
		if($sql){
			if($GLOBALS["jp7_app"])$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
			else $rs=interadmin_query($sql);		
			$row=$rs->FetchNextObj();
			$this->records=$row->records;
			$rs->Close();
		}else{
			if($records)
				$this->records=$records;
			else
				return '[aa]';
		}
		
		$this->total=ceil($this->records/$limit);
		$this->page=$page;
		//$this->sql_limit=" LIMIT ".(($page-1)*$limit).",".$limit;
		$this->limit=$limit;
		$this->init=(($page-1)*$limit);
		
		// HTM
		global $QUERY_STRING;
		
		$this->query_string=preg_replace("(&p_page=[0-9]+)","",$QUERY_STRING);		
		$this->query_string=str_replace("go_url=".$_GET["go_url"],"",$this->query_string);
		//$this->query_string=substr($this->query_string,1);
		global $_POST;
		foreach($_POST as $key=>$value){
			if($key!="p_page")$this->query_string.="&".$key."=".$value;
		}
		
		if($this->total){
			if($this->total>1){
				// Numbers
				$this->htm_numbers_extra=$this->htm_numbers="<div class=\"numbers\"><ul>";
				$min=$page;
				$max=$min+$numbers_limit-1;
				if($max>$this->total){
					$min=$this->total-$numbers_limit+1;
					$max=$this->total;
				}
				if($min<1){
					$min=1;
				}
				if($page!=1&&$this->total>2&&$page>2)$this->htm_numbers_extra.="<li class=\"".(($page==1)?"back-off":"bgleft_plus")."\" onclick=\"location='?".$parameters.$this->query_string."&p_page=1'\">".$back_char_plus."</li>";
				$this->htm_numbers_extra.="<li class=\"".(($page==1)?"back-off":"bgleft")."\" onclick=\"location='?".$parameters.$this->query_string."&p_page=".($page-1)."'\">".$back_char."</li>";
				for($i=$min;$i<=$max;$i++){
					$this->htm_numbers.="<li".(($i==$page)?" class=\"on\"":"")." onclick=\"location='?".$parameters.$this->query_string."&p_page=".$i."'\">".$i."</li>";
					$this->htm_numbers_extra.="<li".(($i==$page)?" class=\"on\"":"")." onclick=\"location='?".$parameters.$this->query_string."&p_page=".$i."'\">".$i."</li>";
					if($i!=$max){$this->htm_numbers.="<li>".$separador."</li>";$this->htm_numbers_extra.="<li>".$separador."</li>";}
				}
				$this->htm_numbers_extra.="<li class=\"".(($page==$this->total)?"go-off":"bgright")."\" onclick=\"location='?".$parameters.$this->query_string."&p_page=".($page+1)."'\">".$go_char."</li>";
				if($page!=$this->total&&$this->total>2&&$page<($this->total-1))$this->htm_numbers_extra.="<li class=\"".(($page==$this->total)?"go-off":"bgright_plus")."\" onclick=\"location='?".$parameters.$this->query_string."&p_page=".$this->total."'\">".$go_char_plus."</li>";
				$this->htm_numbers_extra.="</ul></div>";
				$this->htm_numbers.="</ul></div>";
			}
			// Combo
			$this->htm_combo="<div class=\"text\">P·gina</div>".
			"<select onchange=\"location='?".$parameters.$this->query_string."&p_page='+this[selectedIndex].value\">\n".
			"<script>jp7_num_combo(1,".$this->total.",".$page.")</script>".
			"</select>\n<div class=\"text\">de ".$this->total."</div>\n";
			// Buttons
			$this->htm_back="<input type=\"button\" class=\"back".(($page==1)?" back-off":"")."\" onclick=\"location='?".$parameters.$this->query_string."&p_page=".($page-1)."'\"".(($page==1)?" disabled":"").">\n";
			$this->htm_go="<input type=\"button\" class=\"go".(($page==$this->total)?" go-off":"")."\" onclick=\"location='?".$parameters.$this->query_string."&p_page=".($page+1)."'\"".(($page==$this->total)?" disabled":"").">\n";
			// Types
			$this->htm="<div class=\"jp7_db_pages\" style=\"width:auto\"><div class=\"".$type."\">\n";
			if($type=="combo")$this->htm.=$this->htm_back.$this->htm_combo.$this->htm_go;
			elseif($type=="numbers-top")$this->htm.=$this->htm_numbers.$this->htm_back.$this->htm_go;
			elseif($type=="numbers-bottom")$this->htm.=$this->htm_back.$this->htm_go.$this->htm_numbers;
			else $this->htm.=$this->htm_back.$this->htm_numbers.$this->htm_go;
			$this->htm.="</div></div>\n";
		}
	}
}

// 2007/07/13 by JP
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

// jp7_db_update (2006/04/18)
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
	$sql="SELECT ".implode(",",$fields_arr_db)." FROM ".$table." WHERE ".$table_id_name."=".$table_id_value;
	$rs = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(),$sql));
	if($row =(array)$rs->FetchNextObj()){
		foreach($fields_arr as $field){
			if(strpos($field,"_")===0){
				$field=substr($field,1);
				eval("\$".$field.".=\"".$row[$field]."\";");
			}
		}
	}
	$rs->Close();
	// Update
	$sql="UPDATE ".$table." SET ";
	for($i=0;$i<count($fields_arr_db);$i++){
		eval("\$field_value=\$".$fields_arr_db[$i].";");
		$sql.=$fields_arr_db[$i]."='".$field_value."'";
		if($i!=count($fields_arr_db)-1)$sql.=",";
	}
	$sql.=" WHERE ".$table_id_name."=".$table_id_value;
	$rs = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(),$sql));
}

// 2007/03/10 by JP
function interadmin_tipos_campos($campos){
	$campos_parameters=array("tipo","nome","ajuda","tamanho","obrigatorio","separador","xtra","lista","orderby","combo","readonly","form","label","permissoes","default");
	$campos=split("{;}",$campos);
	for($i=0;$i<count($campos);$i++){
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

// interadmin_tipos_campo (2004/11/03)
function interadmin_tipos_campo($db_prefix,$id_tipo,$var_key){
	global $db;
	global $db_name;
	global $tipo_campos;
	global $tipo_model_id_tipo;
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

// 2007/04/25 by JP
function interadmin_mysql_query($sql,$sql_db="",$sql_debug=false){
	return interadmin_query($sql,$sql_db,$sql_debug);
}

// 2007/03/04 by JP
function interadmin_query($sql,$sql_db="",$sql_debug=false,$numrows=null,$offset=null){	
	global $c_publish;
	global $c_path_upload;
	global $s_interadmin_user;
	global $s_interadmin_preview;
	global $db;
	global $db_prefix;
	global $lang;
		
	$DbNow=$db->BindTimeStamp(date("Y-m-d H:i:s"));
	
	// Debug
	if($sql_debug||($GLOBALS[debug_sql]&&$GLOBALS[c_jp7])){
		$sql_original_debug=preg_replace(array('/(SELECT )/','/( FROM )/','/( WHERE )/','/( ORDER BY )/'),'<b>\1</b>',$sql,1);
		echo "<div style=\"width:auto;color:#ccc;background:#333;border:1px solid gray;font-weight:normal\">".$sql_original_debug."</div>";
	}
	// Split
	$sql_slipt=preg_replace(array('/([	 ])(FROM )/','/([	 ])(WHERE )/','/([ 	])(ORDER BY )/'),'{;}\1\2',$sql,1);
	$sql_slipt=explode("{;}",$sql_slipt);
	foreach($sql_slipt as $value){
		if(!$sql_select&&strpos($value,"SELECT ")!==false)$sql_select=$value;
		if(!$sql_from&&strpos($value,"FROM ")!==false)$sql_from=$value;
		if(!$sql_where&&strpos($value,"WHERE ")!==false)$sql_where=$value;
		if(!$sql_final&&strpos($value,"ORDER BY ")!==false)$sql_final=$value;
	}
	// Parser
	preg_match_all("(([^ ,]+) AS ([^ ,]+))",$sql_from,$out,PREG_PATTERN_ORDER);
	if(count($out[1])){
		// Com Alias
		foreach($out[1] as $key=>$value){
			$alias=$out[2][$key];
			if(strpos($value,$db_prefix."_tipos")!==false)$sql_where=str_replace("WHERE ","WHERE ".$alias.".mostrar<>'' AND (".$alias.".deleted_tipo='' OR ".$alias.".deleted_tipo IS NULL) AND ",$sql_where);
			elseif(strpos($value,$db_prefix.$lang->prefix."_arquivos")!==false||strpos($value,$db_prefix."_arquivos")!==false)$sql_where=str_replace("WHERE ","WHERE ".$alias.".mostrar<>'' AND (".$alias.".deleted='' OR ".$alias.".deleted IS NULL) AND ",$sql_where);
			else $sql_where=str_replace("WHERE ","WHERE ".$alias.".date_publish<='".$DbNow."' AND ".$alias.".char_key<>'' AND (".$alias.".deleted='' OR ".$alias.".deleted IS NULL)".(($c_publish&&!$s_interadmin_preview)?" AND ".$alias.".publish<>''":"")." AND ",$sql_where);
			if($c_path_upload)$sql_select=preg_replace('/([ ,])'.$alias.'.file_([0-9])/','\1REPLACE('.$alias.'.file_\2,\'../../upload/\',\''.$c_path_upload.'\') AS file_\2',$sql_select);
		}
	}else{
		// Sem Alias
		preg_match_all("([ ,]+[".$db_prefix."][^ ,]+)",$sql_from,$out,PREG_PATTERN_ORDER);
		foreach($out[0] as $key=>$value){
			if(strpos($value,$db_prefix."_tipos")!==false)$sql_where=str_replace("WHERE ","WHERE mostrar<>'' AND (deleted_tipo='' OR deleted_tipo IS NULL) AND ",$sql_where);
			elseif(strpos($value,$db_prefix.$lang->prefix."_arquivos")!==false||strpos($value,$db_prefix."_arquivos")!==false)$sql_where=str_replace("WHERE ","WHERE mostrar<>'' AND (deleted LIKE '' OR deleted IS NULL) AND ",$sql_where);
			else $sql_where=str_replace("WHERE ","WHERE date_publish<='".$DbNow."' AND char_key<>'' AND (deleted LIKE '' OR deleted IS NULL)".(($c_publish&&!$s_interadmin_preview)?" AND publish<>''":"")." AND ",$sql_where);
		}
		if($c_path_upload)$sql_select=preg_replace('/([ ,])file_([0-9])/','\1REPLACE(file_\2,\'../../upload/\',\''.$c_path_upload.'\') AS file_\2',$sql_select);
	}
	// Join
	$sql=$sql_select.$sql_from.$sql_where.$sql_final;
	// Debug
	if($sql_debug||($GLOBALS[debug_sql]&&$GLOBALS[c_jp7])){
		$sql_debug=preg_replace(array('/(SELECT )/','/( FROM )/','/( WHERE )/','/( ORDER BY )/'),'<b>\1</b>',$sql,1);
		echo "<div style=\"width:auto;color:#333;background:#ccc;border:1px solid gray;font-weight:normal\">".$sql_debug."</div>";
	}
	// Return
	//if($db_type){
		if($sql_db){
			
			if(isset($numrows) && isset($offset))
				$rs_pre=$sql_db->SelectLimit($sql,$numrows,$offset)or die(jp7_debug($db->ErrorMsg(),$sql));
			else
			$rs_pre=$sql_db->Execute($sql)or die(jp7_debug($sql_db->ErrorMsg(),$sql));
				
		} else{
		
			if(isset($numrows) && isset($offset))
				$rs_pre=$db->SelectLimit($sql,$numrows,$offset)or die(jp7_debug($db->ErrorMsg(),$sql));
		else
			$rs_pre=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
				
		}
	/*}else{
		if($sql_db)
			$rs_pre=mysql_query($sql,$sql_db) or die(jp7_debug(mysql_error(), $sql));
		else
			$rs_pre=mysql_query($sql,$db) or die(jp7_debug(mysql_error(), $sql));
	}*/
			
	if($rs&&$sql)eval("global \$".$rs.";\$".$rs."=\$rs_pre;");
	else return $rs_pre;
}

// interadmin_mysql_query (2007/01/26 by JP)
/*
function interadmin_mysql_query($rs,$sql="",$debug=false){
	global $c_publish;
	global $s_interadmin_user;
	global $s_interadmin_preview;
	global $db;
	global $db_prefix;
	global $lang;
	global $c_path_upload;
	if(!$sql){
		$sql=$rs;
		$rs="";
	}
	$pos1=strpos($sql," FROM ")+6;
	$pos2=strpos($sql," ",$pos1);
	$table=substr($sql,$pos1,$pos2-$pos1);
	// Inner Join
	$pos2=strpos($sql,$table." AS",$pos1);
	if($pos1&&$pos2){
		$pos1=strpos($sql," AS ",$pos2)+4;
		$pos2=strpos($sql," ",$pos1);
		$pos3=strpos($sql,",",$pos1);
		if($pos3!==false&&$pos3<$pos2)$pos2=$pos3;
		$alias=substr($sql,$pos1,$pos2-$pos1).".";
		// Alias 2
		$pos2=strpos($sql,$table." AS",$pos1);
		if($pos2){
			$pos1=strpos($sql," AS ",$pos2)+4;
			$pos2=strpos($sql," ",$pos1);
			$pos3=strpos($sql,",",$pos1);
			if($pos3!==false&&$pos3<$pos2)$pos2=$pos3;
			$alias_2=substr($sql,$pos1,$pos2-$pos1).".";
		}
	}
	// /Inner Join
	// Upload Path
	if($c_path_upload){
		$pos1=strpos($sql," WHERE ");
		if($pos1){
			$sql_parte_2=substr($sql,$pos1);
			$sql=substr($sql,0,$pos1);
		}
		$sql=str_replace(" file_1"," REPLACE(file_1,'../../upload/','".$c_path_upload."') AS file_1",$sql);
		$sql=str_replace(" file_2"," REPLACE(file_2,'../../upload/','".$c_path_upload."') AS file_2",$sql);
		$sql=str_replace(" file_3"," REPLACE(file_3,'../../upload/','".$c_path_upload."') AS file_3",$sql);
		$sql=str_replace(" file_4"," REPLACE(file_4,'../../upload/','".$c_path_upload."') AS file_4",$sql);
		$sql=str_replace(",file_1",",REPLACE(file_1,'../../upload/','".$c_path_upload."') AS file_1",$sql);
		$sql=str_replace(",file_2",",REPLACE(file_2,'../../upload/','".$c_path_upload."') AS file_2",$sql);
		$sql=str_replace(",file_3",",REPLACE(file_3,'../../upload/','".$c_path_upload."') AS file_3",$sql);
		$sql=str_replace(",file_4",",REPLACE(file_4,'../../upload/','".$c_path_upload."') AS file_4",$sql);
		if($alias){
			$sql=str_replace(",".$alias."file_1",",REPLACE(".$alias."file_1,'../../upload/','".$c_path_upload."') AS file_1",$sql);
			$sql=str_replace(",".$alias."file_2",",REPLACE(".$alias."file_2,'../../upload/','".$c_path_upload."') AS file_2",$sql);
			$sql=str_replace(",".$alias."file_3",",REPLACE(".$alias."file_3,'../../upload/','".$c_path_upload."') AS file_3",$sql);
			$sql=str_replace(",".$alias."file_4",",REPLACE(".$alias."file_4,'../../upload/','".$c_path_upload."') AS file_4",$sql);
		}
		if($sql_parte_2)$sql.=$sql_parte_2;
	}
	if($table==$db_prefix||$table==$db_prefix.$lang->prefix){ // Check Table
		if(
			$c_publish // Check Publish
			&&!$s_interadmin_preview // Check Preview
		){
			$sql=str_replace("WHERE ","WHERE ".$alias."publish<>'' AND ",$sql);
		}
		$sql=str_replace("WHERE ","WHERE ".$alias."date_publish<='".date("Y/m/d H:i:s")."' AND ".$alias."char_key<>'' AND ".$alias."deleted='' AND ",$sql);
		if($alias_2)$sql=str_replace("WHERE ","WHERE ".$alias_2."date_publish<='".date("Y/m/d H:i:s")."' AND ".$alias_2."char_key<>'' AND ".$alias_2."deleted='' AND ",$sql);
		if(strpos($sql,$db_prefix."_tipos AS")!==false)$sql=str_replace("WHERE ","WHERE mostrar<>'' AND deleted_tipo='' AND ",$sql);
	}elseif($table==$db_prefix."_tipos"){
		$sql=str_replace("WHERE ","WHERE mostrar<>'' AND deleted_tipo='' AND ",$sql);
	}elseif(strpos($table,$db_prefix)===0&&strpos($table,"_arquivos")===false){
		$sql=str_replace("WHERE ","WHERE ".$alias."date_publish<='".date("Y/m/d H:i:s")."' AND ".$alias."char_key<>'' AND ".$alias."deleted='' AND ",$sql);
	}elseif($table==$db_prefix.$lang->prefix."_arquivos"){
		$sql=str_replace("WHERE ","WHERE mostrar<>'' AND deleted='' AND ",$sql);
	}
	if($GLOBALS[debug_sql]&&$GLOBALS[c_jp7])echo "<hr>".$sql."<hr>";
	$rs_pre=mysql_query($sql,$db)or print(mysql_error());
	// Old Way (2003/04/08)
	//$rs_pre=mysql_query((!$c_publish||($s_interadmin_preview&&$s_interadmin_user)||($table!=$db_prefix&&$table!=$db_prefix.$lang->prefix))?$sql:str_replace("WHERE ","WHERE publish<>'' AND ",$sql),$db)or die(mysql_error());
	// /Old Way (2003/04/08)
	if($rs&&$sql)eval("global \$".$rs.";\$".$rs."=\$rs_pre;");
	else return $rs_pre;
}
*/

// interadmin_tipos_nome (2008/01/09 by JP)
function interadmin_tipos_nome($id_tipo,$nolang=false){
	if(!$id_tipo)return false;
	elseif(is_numeric($id_tipo)){
		global $db;
		global $db_prefix;
		global $lang;
		$sql="SELECT nome,nome".$lang->prefix." AS nome_lang FROM ".$db_prefix."_tipos WHERE id_tipo=".$id_tipo;
		$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(), $sql));
		$row=$rs->FetchNextObj();
		$nome=($row->nome_lang&&!$nolang)?$row->nome_lang:$row->nome;
		$rs->Close();
		return $nome;
	}else{
		return "Tipos";
	}
}

// interadmin_list (2007/01/27 by JP)
function interadmin_list($table,$id_tipo,$id,$type="list",$order="int_key,date_publish,varchar_key",$field="varchar_key",$sql_where=""){
	global $db;
	global $db_name;
	global $s_interadmin_preview;
	global $l_selecione;
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
	$sql="SELECT id,".$field." AS field FROM ".$table.
	" WHERE id_tipo=".$id_tipo.
	" AND char_key<>''".
	(($s_interadmin_preview)?"":" AND publish<>''").
	" AND (deleted='' OR deleted IS NULL)".
	" AND date_publish<='".date("Y/m/d H:i:s")."'".
	$sql_where.
	" ORDER BY ".$order;
	$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
	while($row=$rs->FetchNextObj()){
		if($type=="combo")$S.="<option value=\"".$row->id."\"".(($row->id==$id)?" selected class=\"on\"":"").">".toHTML($row->field)."</option>\n";
		else $S.="<li".(($row->id==$id)?" class=\"on\"":"")."><a href=\"?id=".$row->id."\">".toHTML($row->field)."</a></li>\n";
	}
	$rs->Close();
	if($type=="list"){
		$S.="".
		"</ul>\n".
		"</div>\n";
	}
	return $S;
}

// 2006/08/24
function interadmin_fields_values($param_0,$param_1="",$param_2=""){
	return jp7_fields_values($param_0,$param_1,$param_2);
}

// 2008/05/19 by JP
function jp7_fields_values($param_0,$param_1="",$param_2="",$param_3="",$OOP = false){
	if (is_numeric($param_0)) {
		// ($id,$field)
		global $db_prefix;
		global $lang;
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
		global $db;
		global $db_name;
		$sql = "SELECT ".$fields.
		" FROM ".$table.
		" WHERE ".$table_id_name."=".$table_id_value;
		if (!$GLOBALS['jp7_app'] && strpos($table, '_tipos') === false) {
			$sql .=	" AND publish <> ''" .
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
			$rs = ($GLOBALS["jp7_app"]=='intermail') ? mysql_query($sql) : interadmin_mysql_query($sql);
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

// jp7_id_value (2006/09/12)
function jp7_id_value($varchar_key,$id_tipo=0){
	global $db;
	global $db_name;
	global $db_prefix;
	global $lang;
	$table=$db_prefix.$lang->prefix;
	$sql="SELECT id FROM ".$table." WHERE".
	" varchar_key='".$varchar_key."'".
	(($id_tipo)?" AND id_tipo=".$id_tipo:"");
	$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
	if($row=$rs->FetchNextObj()){
		$I=$row->id;
	}
	$rs->Close();
	return $I;
}

// 2007/08/08 by JP
class jp7_lang{
	function jp7_lang($lang="pt-br",$force=false){
		if($force)$this->lang=$lang;
		else{
			global $REQUEST_URI;
			global $SCRIPT_NAME;
			global $QUERY_STRING;
			global $c_path;
			global $c_site;
			$this->lang=($REQUEST_URI)?$REQUEST_URI:$SCRIPT_NAME;
			if($QUERY_STRING){
				$pos1=strpos($this->lang,$QUERY_STRING);
				if($pos1!==false)$this->lang=substr($this->lang,0,$pos1);
			}
			$this->lang=explode("/",$this->lang);
			//if($c_path){ // Old Way
				$path_size=explode("/",$c_path);
				$path_size=count($path_size);
				//$this->lang=$this->lang[$path_size]; // Old Way
				$this->lang=$this->lang[count($this->lang)-3]; // For Hotsites
			//}else $this->lang=$this->lang[1]; // Old Way
			$this->lang=str_replace("_","",$this->lang); // Apache Redirect
		}
		$langs=Array('en','es','pt-br','pt','fr','jp'); 
		//if(!$this->lang||$this->lang=="pt-br"||$this->lang=="site"||$this->lang==$c_site||$this->lang=="hotsites"||$this->lang=="_hotsites"||$this->lang=="intranet"||$this->lang=="extranet"||$this->lang=="wap"){
		if(!in_array($this->lang,$langs)||$this->lang=='pt-br'){
			$this->lang=$lang;
			$this->prefix="";
			$this->path="";
			$this->path_2="site/";
		}else{
			$this->prefix="_".$this->lang;
			$this->path=$this->lang."/";
			$this->path_2=$this->path;
		}
	}
}

// 2007/07/10 by Thiago
class interadmin_tipos{
	function interadmin_tipos_tipos($id_tipo){
		global $db;
		global $db_prefix;
		global $lang;
		settype($id_tipo,'integer');
		$sql="SELECT parent_id_tipo,model_id_tipo,nome,nome".(($lang->lang!="pt-br")?"_".$lang->lang:"")." AS nome_lang,template,menu,busca,restrito,admin FROM ".$db_prefix."_tipos WHERE id_tipo=".$id_tipo;
		$rs=interadmin_query($sql);
		while($row=$rs->FetchNextObj()){
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
	function interadmin_tipos($id_tipo,$id=0,$replaceGlobals=false){
		global $db;
		global $db_prefix;
		global $lang;
		global $id_nome;
		// Id
		if($id&&is_numeric($id)){
			$sql="SELECT id_tipo,parent_id,varchar_key FROM ".$db_prefix.$lang->prefix." WHERE id=".$id;
			$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
			while($row=$rs->FetchNextObj()){
				$id_tipo=$row->id_tipo;
				$parent_id=$row->parent_id;
				$id_nome=$row->varchar_key;
			}
			$rs->Close();
		}
		// Parent Id
		if($parent_id&&is_numeric($parent_id)){
			$sql="SELECT id_tipo,parent_id FROM ".$db_prefix.$lang->prefix." WHERE id=".$parent_id;
			$rs = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(),$sql));
			while($row=$rs->FetchNextObj()){
				$id_tipo=$row->id_tipo;
				$grand_parent_id=$row->parent_id;
			}
			$rs->Close();
		}
		// Grand Parent Id
		if($grand_parent_id&&is_numeric($grand_parent_id)){
			$sql="SELECT id_tipo FROM ".$db_prefix.$lang->prefix." WHERE id=".$grand_parent_id;
			$rs = $db->Execute($sql) or die(jp7_debug($db->ErrorMsg(),$sql));
			while($row=$rs->FetchNextObj()){
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
	}
}

// interadmin_id_tipo (2007/05/23)
function interadmin_id_tipo($id="",$parent_id_tipo=0,$model_id_tipo=0){
	global $db;
	global $db_prefix;
	global $lang;
	if($id){
		$sql="SELECT id_tipo FROM ".$db_prefix.$lang->prefix;
		" WHERE id=".$id;
	}else{
		$sql="SELECT id_tipo FROM ".$db_prefix."_tipos".
		" WHERE parent_id_tipo=".$parent_id_tipo.
		(($model_id_tipo)?" AND model_id_tipo=".$model_id_tipo:"").
		" ORDER BY ordem,nome";
	}
	$sql.=" LIMIT 1";
	$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(), $sql));
	if($row=$rs->FetchNextObj()){
		return $row->id_tipo;
	}
	$rs->Close();
}

// interadmin_cabecalho (2006/11/29)
class interadmin_cabecalho{
	function interadmin_cabecalho($i=0,$model_id_tipo=5,$check="file_1,file_2",$rand=false){
		global $db;
		global $db_prefix;
		global $tipos;
		if($id_tipo=interadmin_id_tipo(0,$tipos->id_tipo[$i],$model_id_tipo)){
			$sql="SELECT varchar_key,varchar_1,varchar_2,file_1,file_2 FROM ".$db_prefix.$lang->prefix.
			" WHERE id_tipo=".$id_tipo.
			" AND char_key<>''".
			" AND publish<>''".
			" AND deleted=''".
			" ORDER BY int_key,date_publish DESC";
			$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(), $sql));
			if($rand)$rand=rand(1,$rs->RecordCount());
			$j=1;
			while($row=$rs->FetchNextObj()){
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


// Other

// jp7_flash (2005/11/18)
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

// jp7_interlog (2003/08/25)
/*
function jp7_interlog($id_cliente){
	global $SERVER_ADDR;
	global $REMOTE_ADDR;
	global $HTTP_USER_AGENT;
	if($REMOTE_ADDR!="127.0.0.1"&&strpos($REMOTE_ADDR,"192.168")!==0){
		ob_start();
		readfile("http://jp7.com.br/interlog/site/aplicacao/acessos_inserir_ok.php?id_cliente=".$id_cliente."&servidor_ip=".$SERVER_ADDR."&visitante_ip=".$REMOTE_ADDR."&visitante_useragent=".urlencode($HTTP_USER_AGENT));
		ob_end_clean();
	}
}
*/

// jp7_interlog (2005/06/09)
function jp7_interlog($id_cliente,$host="jp7.com.br",$db_name_interlog="interlog"){
	global $SERVER_ADDR;
	global $LOCAL_ADDR;
	global $REMOTE_ADDR;
	global $HTTP_HOST;
	global $HTTP_USER_AGENT;
	global $c_site;
	global $c_server_type;
	global $db;
	global $db_name;
	if(!$SERVER_ADDR)$SERVER_ADDR=$LOCAL_ADDR;
	if($REMOTE_ADDR!="127.0.0.1"&&strpos($REMOTE_ADDR,"192.168")!==0){
	//if($REMOTE_ADDR!="127.0.0.1"&&strpos($REMOTE_ADDR,"192.168")!==0&&($c_server_type=="Principal"||$host=="localhost")){
		if($host=="localhost"){
			if(!$db){
				$only_info=true;
				include "inc/connection_open.php";
				$db=mysql_connect($db_host,$db_user,$db_pass)or print(mysql_error());
			}
			if($db){
				$servidor_date=date("Y/m/d H:i:s");
				$separador="{;}";
				$dados=$id_cliente.$separador.$SERVER_ADDR.$separador.$servidor_date.$separador.$REMOTE_ADDR.$separador.$HTTP_USER_AGENT;
				mysql_select_db($db_name_interlog,$db);
				mysql_query("INSERT INTO interlog_".$c_site." (dados) VALUES ('".$dados."')",$db)or print(mysql_error());
				mysql_select_db($db_name,$db);
			}
			if($only_info)mysql_close($db);
		}else{
			ob_start();
			readfile("http://".$host."/interlog/site/aplicacao/acessos_inserir_ok.php?id_cliente=".$id_cliente."&servidor_ip=".$SERVER_ADDR."&visitante_ip=".$REMOTE_ADDR."&visitante_useragent=".urlencode($HTTP_USER_AGENT));
			ob_end_clean();
		}
	}
}

// jp7_path (2003/08/25)
function jp7_path($S){
	return (strrpos($S,"/")+1==strlen($S)||!$S)?$S:$S."/";
}

// jp7_doc_root (2005/09/22)
function jp7_doc_root(){
	global $DOCUMENT_ROOT;
	global $PATH_TRANSLATED;
	global $PATH_INFO;
	global $c_jp7;
	global $c_path;
	$S=$DOCUMENT_ROOT;
	if(!$S)$S=@ini_get('doc_root');
	if(!$S){
		$S=dirname($PATH_TRANSLATED);
		if($c_jp7){
			$S=str_replace("\\","/",$S);
			$S=str_replace("//","/",$S);
			$S=substr($S,0,strpos($S,dirname($PATH_INFO)));
		}
	}
	if(!$S){
		$S=realpath("./");
		$S=substr($c_root,0,($c_path)?strpos($S,$c_path):strpos($S,"site"));
	}
	$S=jp7_path($S);
	return $S;
}

// jp7_include (2005/04/29)
function jp7_include($file){
	if(!@include "../../".$file)@include jp7_doc_root().$file;
}

// jp7_path_find (2005/05/01)
function jp7_path_find($file){
	$path="";
	$ok=false;
	$i=0;
	while(!$ok&&$i<5){
		$path.="../";
		$ok=@file_exists($path.$file);
		$i++;
	}
	if(!$ok)return jp7_doc_root().$file;
	else return $path.$file;
}

// jp7_extension (2003/08/25)
function jp7_extension($S){
	$path_parts=pathinfo($S);
	$ext=trim($path_parts["extension"]." ");
	return (!$ext)?"---":$ext;
}

// 2007/08/01 by JP
function jp7_mail($to,$subject,$message,$headers="",$parameters="",$template="",$html=true,$attachments=""){
	// TEXT
	if(strpos($message,"<br>")!==false){
		$text_hr="";
		for($i=0;$i<80;$i++){
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
			global $HTTP_HOST;
			global $SCRIPT_NAME;
			@ini_set("allow_url_fopen","1");
			if((!dirname($template)||dirname($template)==".")&&@ini_get("allow_url_fopen")){
				$template="http://".$HTTP_HOST.dirname($SCRIPT_NAME)."/".$template;
			}
			if($pos1=strpos($template,"?")){
				//$template=substr($template,0,$pos1+1).urlencode(substr($template,$pos1+1));
				$template=str_replace(" ","%20",$template);
			}
			
			//valida usu·rio logado e caso o template inicie em http
			if($_SERVER["PHP_AUTH_USER"]){
				$template=str_replace("http://","http://".$_SERVER["PHP_AUTH_USER"].":".$_SERVER["PHP_AUTH_PW"]."@", $template);
			}
			
			//echo "template: ".$template;
			if(function_exists("file_get_contents")){
				$template=file_get_contents($template);
			}else{
			ob_start();
			readfile($template);
			$template=ob_get_contents();
			ob_end_clean();
			}
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
	global $debug;if($debug)echo "jp7_mail(".htmlentities($to)."): ".$mail."<br>";
	return $mail;
}

// jp7_image_text (2006/04/07)
function jp7_image_text($filename_src,$filename_dst,$size,$angle,$x,$y,$col,$fontfile,$text,$padding="0 0 0 0",$shadow=false,$antialiasing=""){
	$im=ImageCreateFromPng($filename_src);
	$col_arr=explode(",",$col);
	$col_arr=ImageColorAllocate($im,$col_arr[0],$col_arr[1],$col_arr[2]);
	if($x!=="center"&&$shadow){
		$shadow_color=explode(",",$shadow[color]);
		if(function_exists('ImageColorAllocateAlpha'))$shadow_color=ImageColorAllocateAlpha($im,$shadow_color[0],$shadow_color[1],$shadow_color[2],$shadow_color[3]);
		else $shadow_color=ImageColorAllocate($im,$shadow_color[0],$shadow_color[1],$shadow_color[2]);
		ImageTTFText($im,$size,$angle,$x+$shadow[x],$y+$shadow[y],$shadow_color,$fontfile,$text);
	}
	ImageTTFText($im,$size,$angle,($x==="center"||$x==="right"||$x==="trim")?0:$x,($y==="center")?0:$y,$antialiasing.$col_arr,$fontfile,$text);
	ImagePng($im,$filename_dst);
	ImageDestroy($im);
	// Center
	if($x==="center"||$y==="center"){
		$im=imageCreateFromPng($filename_dst);
		$padding=explode(" ",$padding);
		$center=imagettfbbox($size,$angle,$fontfile,$text);
		if($x==="center"){
			$x=$center[4]+1;
			$x=(ImageSX($im)-$x-$padding[3])/2;
		}
		if($y=="center"){
			$y=$center[5]+1;
			$y=(ImageSY($im)-$y-$padding[0])/2;
		}
		if($x!=="center")jp7_image_text($filename_src,$filename_dst,$size,$angle,$x,$y,$col,$fontfile,$text,"",$shadow,$antialiasing);
		ImageDestroy($im);
	// Right
	}elseif($x==="right"){
		$im=imageCreateFromPng($filename_dst);
		$padding=explode(" ",$padding);
		$right=imagettfbbox($size,$angle,$fontfile,$text);
		if($x==="right"){
			$x=$right[4];
			$x=(ImageSX($im)-$x-$padding[1]);
		}
		if($x!=="right")jp7_image_text($filename_src,$filename_dst,$size,$angle,$x,$y,$col,$fontfile,$text,"",$shadow,$antialiasing);
		ImageDestroy($im);
	// Trim
	}elseif($x==="trim"){
		$im=imageCreateFromPng($filename_dst);
		$padding=explode(" ",$padding);
		$x=imagettfbbox($size,$angle,$fontfile,$text);
		$x=$x[4]+1;
		if($x!=="trim"){
			$im2=imageCreate($x+$padding[1]+$padding[3],imagesy($im));
			$im_bg=imagecolorsforindex($im,imagecolorat($im,1,1));
			$im_bg=imagecolorallocate($im2,$im_bg["red"],$im_bg["green"],$im_bg["blue"]);
			imagefill($im2,0,0,$im_bg);
			imagecolortransparent($im2,$im_bg);
			imagecopymerge($im2,$im,$padding[1],0,0,0,$x+$padding[1]+$padding[3],ImageSY($im),100);
			imagepng($im2,$filename_dst);
			imageDestroy($im2);
		}
		ImageDestroy($im);
	}
}

// jp7_resizeImage (2006/08/17)
function jp7_resizeImage($im_src,$src,$dst,$w,$h,$q=90,$s=10000000){
	$c_gd=function_exists("imagecreatefromjpeg");
	// Check Size and Orientation (Horizontal x Vertical)
	if($c_gd){
		// GD Get Size
		$src_w=imagesx($im_src);
		$src_h=imagesy($im_src);
	}else{
		// Magick Get Size
		$command="/usr/bin/identify -verbose ".$src;
		exec($command,$a,$b);
		$src_geometry=split("x",substr($a[2],strpos($a[2],":")+2));
		$src_w=$src_geometry[0];
		$src_h=$src_geometry[1];
	}
	if(!$q)$q=90;
	if(!$s)$s=10000000;// 10 MB
	if($w==$h){
		$dst_w=$w;
		$dst_h=$h;
		if($src_w>$src_h)$src_w=$src_h;
		else $src_h=$src_w;
	}else{
		if($src_w>$src_h){
			$dst_w=$w;
			$dst_h=intval(round(($dst_w*$src_h)/$src_w));
		}else{
			$dst_h=$w;
			$dst_w=intval(round(($dst_h*$src_w)/$src_h));
		}
	}
	if($dst_w>=$src_w&&$dst_h>=$src_h){
		// No-Resize and Check Weight
		if(filesize($src)/1000>$s){
			$im_dst=$im_src;
			if($c_gd){
				// GD Convert Quality
				imagejpeg($im_dst,$dst,$q);
			}else{
				// Magick Convert Quality
				$command="/usr/bin/convert ".$src." -quality ".$q." +profile '*' ".$dst;
				exec($command,$a,$b);
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

// jp7_encode_mimeheader (2005/12/08)
function jp7_encode_mimeheader($S,$charset="iso-8859-1",$transfer_encoding="Q"){
	return (function_exists("mb_encode_mimeheader"))?mb_encode_mimeheader($S,$charset,$transfer_encoding):$S;
}

// jp7_index (2008/01/11 by JP)
function jp7_index($lang=""){
	session_start();
	global $HTTP_HOST;
	global $HTTP_ACCEPT;
	global $HTTP_USER_AGENT;
	global $is;
	global $path;
	global $publish;
	global $s_interadmin_preview;
	$path=dirname($_SERVER["SCRIPT_NAME"]);
	$path=jp7_path("http://".$HTTP_HOST.$path);
	// Publish Check
	$admin_time=@filemtime("interadmin.log");
	$index_time=@filemtime("site/home/index_P.htm");
	if($admin_time>$index_time||date("d")!=date("d",$index_time))$publish=true;
	// Redirect
	//if(strpos($HTTP_ACCEPT,"/vnd.wap")!==false)header("Location: ".$path."wap/home/index.php");
	//elseif($is->v<4&&!$is->robot)header("Location: /_default/oldbrowser.htm");
	//else{
		$path=$path.(($lang&&$lang!="pt-br")?$lang:"site")."/home/".(($publish||!$admin_time||!$index_time)?"index.php":"index_P.htm").(($s_interadmin_preview)?"?s_interadmin_preview=".$s_interadmin_preview:"");
		@ini_set("allow_url_fopen","1");
		//if(!@include $path.(($s_interadmin_preview)?"&":"?")."HTTP_USER_AGENT=".urlencode($HTTP_USER_AGENT))header("Location: ".$path);
		if(!@readfile($path.(($s_interadmin_preview)?"&":"?")."HTTP_USER_AGENT=".urlencode($HTTP_USER_AGENT)))header("Location: ".$path);
	//}
}

// jp7_host (2005/08/10)
function jp7_host($hosts){
	global $HTTP_HOST;
	$hosts=explode(",",$hosts);
	foreach($hosts as $host){
		if(strpos($HTTP_HOST,$host)!==false){
			return true;
			exit;
		}
	}
}

function getFileName($filename){
	return ($GLOBALS["c_jp7"])?$filename:"";
}

/* get file size */

function jp7_file_size($file){
	$file = ceil(@filesize($file)/1000);
	$file = ($url_size<1000)?ceil($file)."KB":round($file/1000,1)."MB";
	return $file;
}

/**
 * Comando para depurar saida de erros
 *
 */  
function jp7_debug($msgErro=null, $sql=null, $sendMail=true){
	$backtrace=debug_backtrace();krsort($backtrace);
	$erroDetalhesArray=reset($backtrace);
	$S="<pre style=\"background-color:#FFFFFF;font-size:11px;text-align:left;padding:10px;\">";	
	$S.="<strong style=\"color:red\">       ERRO:</strong> ".$msgErro."\n";
	$S.="<strong style=\"color:red\">    ARQUIVO:</strong> ".$erroDetalhesArray['file']."\n";	
	$S.="<strong style=\"color:red\">      LINHA:</strong> ".$erroDetalhesArray['line']."\n";	
	$S.="<strong style=\"color:red\">        URL:</strong> ".(($_SERVER['HTTPS']=='on')?"https://":"http://").$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]."\n";	
	if($_SERVER["HTTP_REFERER"])	
	$S.="<strong style=\"color:red\">    REFERER:</strong> ".$_SERVER["HTTP_REFERER"]."\n";	
	$S.="<strong style=\"color:red\">         IP:</strong> ".$_SERVER["REMOTE_ADDR"]."\n";
	$S.="<strong style=\"color:red\"> USER_AGENT:</strong> ".$_SERVER['HTTP_USER_AGENT']."\n";
	if($sql) 
	$S.="<strong style=\"color:red\">        SQL:</strong> ".$sql."\n";
	$S.="<strong style=\"color:red\">  BACKTRACE:</strong> ".print_r($backtrace,true);
	if(count($_POST))
	$S.="<strong style=\"color:red\">       POST:</strong> ".print_r($_POST,true);	
	if(count($_GET))
	$S.="<strong style=\"color:red\">        GET:</strong> ".print_r($_GET,true);
	if(count($_SESSION))
	$S.="<strong style=\"color:red\">    SESSION:</strong> ".print_r($_SESSION,true);
	if(count($_COOKIE))
	$S.="<strong style=\"color:red\">     COOKIE:</strong> ".print_r($_COOKIE,true);

	$S.="</pre>";
	
	//Envia email
	if($GLOBALS['c_server_type']=="Principal"){
		if(trim($GLOBALS['c_site']))
			$cliente = $GLOBALS['c_site'] . ']';
		elseif(trim($_SESSION['s_interadmin_cliente']))
			$cliente = $_SESSION['s_interadmin_cliente'] . ']';
		elseif(trim($_COOKIE['cookie_interadmin_cliente']))
			$cliente = $_COOKIE['cookie_interadmin_cliente'];
		$subject = '['. $cliente . '][' . ($GLOBALS['jp7_app']) ? $GLOBALS['jp7_app'] : 'Site' . '][Erro]';
		$message = "Ocorreram erros no InterAdmin<br />" . $S;
		$headers = "To: " . $to . " <" . $to . ">\r\n";
		$headers .= "From: " . $to . " <" . $to . ">\r\n";
		$parameters = "";
		//$template="form_htm.php";
		$html=true;
		$to='debug+' . $cliente . '@jp7.com.br';
		jp7_mail($to,$subject,$message,$headers,$parameters,$template,$html);
		if($GLOBALS['c_server_type']=="Principal"){
			$S = "Ocorreu um erro ao tentar acessar esta p·gina, se o erro persistir envie um email para <a href=\"debug@jp7.com.br\">debug@jp7.com.br</a>";
			header("Location: /em_manutencao.htm");
			//Caso nao funcione o header, tenta por javascript
			?>
            <script language="javascript" type="text/javascript">
			document.location.href="/em_manutencao.htm";
			</script>
            <?
			exit();
		}
	}
	return $S;	
}

/**
 * XOR encrypts a given string with a given key phrase.
 *
 * @param     string    $InputString    Input string
 * @param     string    $KeyPhrase      Key phrase
 * @return    string    Encrypted string    
 */    
function XOREncryption($InputString, $KeyPhrase){
 
    $KeyPhraseLength = strlen($KeyPhrase);
 
    // Loop trough input string
    for ($i = 0; $i < strlen($InputString); $i++){
 
        // Get key phrase character position
        $rPos = $i % $KeyPhraseLength;
 
        // Magic happens here:
        $r = ord($InputString[$i]) ^ ord($KeyPhrase[$rPos]);
 
        // Replace characters
        $InputString[$i] = chr($r);
    }
 
    return $InputString;
}
 
// Helper functions, using base64 to
// create readable encrypted texts:
 
function XOREncrypt($InputString, $KeyPhrase){
    $InputString = XOREncryption($InputString, $KeyPhrase);
    $InputString = urlencode($InputString);
    return $InputString;
}
 
function XORDecrypt($InputString, $KeyPhrase){
    $InputString = urldecode($InputString);
    $InputString = XOREncryption($InputString, $KeyPhrase);
    return $InputString;
}


// Autoload
/*
// Carrega Classes
define("ROOT_DIR",dirname(__FILE__)."/");

function __autoload($className){
   //$folder=classFolder($className);
   //if($folder)require_once($folder."/".$className.".php")
	 $include = require_once(jp7_doc_root().'classes/'.$className.".class.php");
	 //if(!$include) require_once(jp7_doc_root().'interaccount/classes/'.$className."class.php");
}

function classFolder($className,$folder="classes") {
   $dir=dir(ROOT_DIR.$folder);
   if($folder=="classes"&&file_exists(ROOT_DIR.$folder."/".$className.".php"))return $folder;
	 else{
	 	while(false!==($entry=$dir->read())){
			$checkFolder=$folder."/".$entry;
			if(strlen($entry)>2){
				if(is_dir(ROOT_DIR.$checkFolder)){
					if(file_exists(ROOT_DIR.$checkFolder."/".$className.".php"))return $checkFolder;
					else{
						$subFolder=classFolder($className,$checkFolder);
						if($subFolder)return $subFolder;
					}
				}
			}
		}
	}
	$dir->close();
  return 0;
}
*/

// Actions
jp7_register_globals();
$c_jp7=($REMOTE_ADDR=="201.6.156.39"||$REMOTE_ADDR=="192.168.0.2"||$REMOTE_HOST=="192.168.0.2"||$LOCAL_ADDR=="192.168.0.2"||$SERVER_ADDR=="192.168.0.2");
?>
