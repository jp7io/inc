<?
// JP7's PHP Application Functions
// Copyright 2004-2006 JP7
// http://jp7.com.br
// Versão 0.05 - 2006/08/29


// jp7_app_checkPermission (2006/08/17)
function jp7_app_checkPermission(){
	global $jp7_app;
	global $c_cliente_domains;
	global $s_interadmin_cliente;
	$cliente = ($s_interadmin_cliente) ?  $s_interadmin_cliente : $GLOBALS['s_' . $jp7_app . '_cliente'];
	$ok = FALSE;
	for($i = 0;$i<count($c_cliente_domains);$i++){
		switch($_SERVER['HTTP_HOST']){
			case $c_cliente_domains[$i]:
			case "www.".$c_cliente_domains[$i]:
			case "www.".toId($c_cliente_domains[$i]):
			case "www2.".$c_cliente_domains[$i]:
			case "interadmin.".$c_cliente_domains[$i]:
			case "intermail.".$c_cliente_domains[$i]:
			case "ri.".$c_cliente_domains[$i]:
			case "ir.".$c_cliente_domains[$i]:
				$ok = TRUE;
				break;
		}
	}
	switch($_SERVER['HTTP_HOST']){
		case "192.168.0.2":
		case "localhost":
		case "jp":
		case "jp7":
		case "jp7.com.br":
		case "www.jp7.com.br":
		case "interadmin.jp7.com.br":
		case "intermail.jp7.com.br":
		case "jpsete.com.br":
		case "www.jpsete.com.br":
		case "jp7.dnsalias.com":
			$ok = TRUE;
			break;
	}
	if(!$ok){
		header("Location:http://jp7.com.br/".$jp7_app."/site/home/ixed.php?cliente=".$cliente);
		exit();
	}
}

// jp7_app_createSelect() (2004/04/29)
function jp7_app_createSelect($name,$label,$div,$start,$finish,$value,$xtra=""){
	$S="".
	"<select name=\"".$name."\"".(($xtra)?" ".$xtra:"").">".
	"<option value=\"\">".$label."</option>".
	"<option value=\"\">".$div."</option>";
	for($i=$start;$i<=$finish;$i++){
		if($i<10)$i="0".$i;
		$S.="<option value=\"".$i."\"".(($i==$value)?" selected=\"selected\"":"").">".$i."</option>";
	}
	$S.="</select>";
	return $S;
}

// jp7_app_createSelect_date() (2007/05/25 by JP)
function jp7_app_createSelect_date($var,$time_xtra="",$s=false,$i=false,$readonly="",$xtra=""){
	global $l_dia, $l_mes, $l_ano, $l_hora, $l_min;
	global $lang;
	$date=jp7_date_split($GLOBALS[$var]);
	if($i!==false)$i="[".$i."]";
	if($GLOBALS["interadmin_visualizar"]){
		if($date[d]!="00"){
			return "".
			"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">".
				"<tr>".
					"<td>".(($date[d]!="00")?$date[d]:"")."</td>".
					"<td>&nbsp;/&nbsp;</td>".
					"<td>".(($date[m]!="00")?$date[m]:"")."</td>".
					"<td>&nbsp;/&nbsp;</td>".
					"<td>".(($date[Y]!="0000")?$date[Y]:"")."</td>".
					"<td".(($time_xtra)?" ".$time_xtra:"")." nowrap>&nbsp;-&nbsp;</td>".
					"<td".(($time_xtra)?" ".$time_xtra:"").">".(($date[H])?$date[H]:"")."</td>".
					"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;:&nbsp;</td>".
					"<td".(($time_xtra)?" ".$time_xtra:"").">".(($date[i])?$date[i]:"")."</td>".
					(($s)?"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;:&nbsp;</td>":"").
					(($s)?"<td><input type=\"text\" name=\"".$var."_s".$i."\" value=\"".$date[s]."\" style=\"color:#ccc;width:20px\"></td>":"").
				"</tr>".
			"</table>";
		}else{
			return "N/D";
		}
	}elseif(strpos($xtra,"nocombo_")!==false){
		$day = "<td><input type=\"text\" name=\"".$var."_d".$i."\" maxlength=\"2\" value=\"".(($date[d]!="00"&&$GLOBALS[$var])?$date[d]:$l_dia)."\" ".$readonly." helpvalue=\"" . $l_dia . "\" style=\"width:3em".(($date[d]=="00"||!$GLOBALS[$var])?";color:#ccc;font-style:italic":"")."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\" onkeyup=\"DFchangeField(this, event)\" /></td>".
				"<td>&nbsp;/&nbsp;</td>";
		$month = "<td><input type=\"text\" name=\"".$var."_m".$i."\" maxlength=\"2\" value=\"".(($date[m]!="00"&&$GLOBALS[$var])?$date[m]:$l_mes)."\" ".$readonly." helpvalue=\"" . $l_mes . "\" style=\"width:3em".(($date[m]=="00"||!$GLOBALS[$var])?";color:#ccc;font-style:italic":"")."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\" onkeyup=\"DFchangeField(this, event)\" /></td>".
				"<td>&nbsp;/&nbsp;</td>";
		return "".
		"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">".
			"<tr>".
				(($lang->lang == 'en') ? $month . $day : $day . $month ) .
				"<td><input type=\"text\" name=\"".$var."_Y".$i."\" maxlength=\"4\" value=\"".(($date[Y]!="0000"&&$GLOBALS[$var])?$date[Y]:$l_ano)."\" ".$readonly." helpvalue=\"" . $l_ano . "\" style=\"width:5em".(($date[Y]=="0000"||!$GLOBALS[$var])?";color:#ccc;font-style:italic":"")."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\"".((!$time_xtra)?" onkeyup=\"DFchangeField(this, event)\"":"")." /></td>".
				"<td".(($time_xtra)?" ".$time_xtra:"")." nowrap>&nbsp;-&nbsp;</td>" .
				"<td><input type=\"text\" name=\"".$var."_H".$i."\" maxlength=\"2\" value=\"".(($date[H]&&$GLOBALS[$var]!="0000-00-00 00:00:00")?$date[H]:$l_hora)."\" ".$readonly." helpvalue=\"" . $l_hora . "\" style=\"width:3em".((!$date[H]||$GLOBALS[$var]=="0000-00-00 00:00:00")?";color:#ccc;font-style:italic":"").(($time_xtra)?";visibility:hidden":"")."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\" onkeyup=\"DFchangeField(this, event)\" /></td>".
				"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;:&nbsp;</td>".
				"<td><input type=\"text\" name=\"".$var."_i".$i."\" maxlength=\"2\" value=\"".(($date[i]&&$GLOBALS[$var]!="0000-00-00 00:00:00")?$date[i]:$l_min)."\" ".$readonly." helpvalue=\"".$l_min."\" style=\"width:3em".((!$date[i]||$GLOBALS[$var]=="0000-00-00 00:00:00")?";color:#ccc;font-style:italic":"").(($time_xtra)?";visibility:hidden":"")."\" onfocus=\"refreshDateStyle(this,'focus')\" onblur=\"refreshDateStyle(this,'blur')\" onkeypress=\"return DFonlyThisChars(true)\" /></td>".
				(($s)?"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;:&nbsp;</td>":"").
				(($s)?"<td><input type=\"text\" name=\"".$var."_s".$i."\" value=\"".$date[s]."\" style=\"color:#ccc;width:20px\"></td>":"").
			"</tr>".
		"</table>";
	}else{
		$day = "<td>".jp7_app_createSelect($var."_d".$i,$l_dia,"---",1,31,$date[d],$readonly)."</td>".
				"<td>&nbsp;/&nbsp;</td>";
		$month = "<td>".jp7_app_createSelect($var."_m".$i,$l_mes,"---",1,12,$date[m],$readonly)."</td>".
				"<td>&nbsp;/&nbsp;</td>"; 
		return "".
		"<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">".
			"<tr>".
				(($lang->lang == 'en') ? $month . $day : $day . $month ) .
				"<td>".jp7_app_createSelect($var."_Y".$i,$l_ano,"---",date(Y)-100,date(Y)+20,$date[Y],$readonly)."</td>".
				"<td".(($time_xtra)?" ".$time_xtra:"")." nowrap>&nbsp;-&nbsp;</td>".
				"<td>".jp7_app_createSelect($var."_H".$i,"H","---",0,23,$date[H],$time_xtra)."</td>".
				"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;:&nbsp;</td>".
				"<td>".jp7_app_createSelect($var."_i".$i,"M","---",0,59,$date[i],$time_xtra)."</td>".
				(($s)?"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;:&nbsp;</td>":"").
				(($s)?"<td><input type=\"text\" name=\"".$var."_s".$i."\" value=\"".$date[s]."\" style=\"color:#ccc;width:20px\"></td>":"").
			"</tr>".
		"</table>";
	}
}

function jp7_app_log($log,$S){
	global $jp7_app;
	global $c_interadminConfigPath;
	if($jp7_app=="intermail"){
		global $s_intermail_cliente;
		global $s_intermail_user;
		$app_cliente=$s_intermail_cliente;
		$app_user=$s_intermail_user;
	}else{
		global $s_interadmin_cliente;
		global $s_user;
		$app_cliente=$s_interadmin_cliente;
		$app_user=$s_user['login'];
	}
	$file_path = $c_interadminConfigPath . '_log/';
	@chmod($file_path,0777);
	$log_file = $file_path . $log . '.log';
	if (file_exists($log_file) && date('d', filemtime($log_file)) == date('d')) {
		ob_start();
		readfile($log_file);
		$file_data=ob_get_contents();
		ob_end_clean();
	}
	$file=fopen($log_file,"w");
	fwrite($file,$file_data.date("d/m/Y H:i")." - ".$app_user." - ".$_SERVER['REMOTE_ADDR']." - ".$S."\r\n");
	fclose($file);
	$log_file_day = $file_path . $log . '_' . date('d') . '.log';
	copy($log_file, $log_file_day);
	@chmod($log_file, 0777);
	@chmod($log_file_day, 0777);
}

// jp7_msg (2003/XX/XX)
function jp7_msg($S,$type){
	include jp7_path_find('inc/msg.php');
}

// jp7_phpmyadmin_path (2004/06/23)
/**
 * @deprecated Não é mais utilizado o phpmyadmin para backup
 */
function jp7_phpmyadmin_path($path="../_admin/phpmyadmin/",$i=0){
	if(is_dir($path)||$i>3)return $path;
	else return jp7_phpmyadmin_path("../".$path,$i++);
}

// jp7_phpmyadmin_aplicacao_path (2007/07/19)
/**
 * @deprecated Não é mais utilizado o phpmyadmin para backup
 */
function jp7_phpmyadmin_aplicacao_path($path="../_admin/phpmyadmin/",$path2="../../"){
	if(is_dir($path)||$i>3){
		global $SCRIPT_NAME;
		global $jp7_app;
		return $path2.((strpos($SCRIPT_NAME,($jp7_app=="intertime"||$jp7_app=="interaccount"||$jp7_app=="intersite"||$jp7_app=="intermail_new")?"interadmin":$jp7_app)===false)?"../":(($jp7_app=="intertime"||$jp7_app=="interaccount"||$jp7_app=="intersite"||$jp7_app=="intermail_new")?"interadmin":$jp7_app)."/");
	}
	else return jp7_phpmyadmin_aplicacao_path("../".$path,"../".$path2);
}
?>
