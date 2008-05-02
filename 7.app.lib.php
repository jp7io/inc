<?
// JP7's PHP Application Functions
// Copyright 2004-2006 JP7
// http://jp7.com.br
// Versão 0.05 - 2006/08/29


// jp7_app_checkPermission (2006/08/17)
function jp7_app_checkPermission(){
	global $HTTP_HOST;
	global $jp7_app;
	global $c_cliente_domains;
	eval("global \$s_".$jp7_app."_cliente;");
	eval("\$cliente=\"\$s_".$jp7_app."_cliente\";");
	$ok=false;
	for($i=0;$i<count($c_cliente_domains);$i++){
		switch($HTTP_HOST){
			case $c_cliente_domains[$i]:
			case "www.".$c_cliente_domains[$i]:
			case "www.".toId($c_cliente_domains[$i]):
			case "www2.".$c_cliente_domains[$i]:
			case "interadmin.".$c_cliente_domains[$i]:
			case "intermail.".$c_cliente_domains[$i]:
			case "ri.".$c_cliente_domains[$i]:
			case "ir.".$c_cliente_domains[$i]:
				$ok=true;
				break;
		}
	}
	switch($HTTP_HOST){
		case "192.168.0.2":
		case "localhost":
		case "jp":
		case "jp7":
		case "jp7.com.br":
		case "www.jp7.com.br":
		case "jpsete.com.br":
		case "www.jpsete.com.br":
		case "jp7.dnsalias.com":
			$ok=true;
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
	"<option>".$label."</option>".
	"<option>".$div."</option>";
	for($i=$start;$i<=$finish;$i++){
		if($i<10)$i="0".$i;
		$S.="<option value=\"".$i."\"".(($i==$value)?" selected":"").">".$i."</option>";
	}
	$S.="</select>";
	return $S;
}

// jp7_app_createSelect_date() (2007/05/23 by JP)
function jp7_app_createSelect_date($var,$time_xtra="",$s=false,$i=false,$readonly="",$xtra=""){
	$date=jp7_date_split($GLOBALS[$var]);
	if($i!==false)$i="[".$i."]";
	if(strpos("nocombo_",$xtra)===0){
		return "".
		"<table border=0 cellspacing=0 cellpadding=0>".
			"<tr>".
				"<td><input type=\"text\" name=\"".$var."_d".$i."\" value=\"".$date[d]."\" ".$readonly."></td>".
				"<td>&nbsp;/&nbsp;</td>".
				"<td><input type=\"text\" name=\"".$var."_m".$i."\" value=\"".$date[m]."\" ".$readonly."></td>".
				"<td>&nbsp;/&nbsp;</td>".
				"<td><input type=\"text\" name=\"".$var."_Y".$i."\" value=\"".$date[Y]."\" ".$readonly."></td>".
				"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;-&nbsp;</td>".
				"<td><input type=\"text\" name=\"".$var."_H".$i."\" value=\"".$date[d]."\" ".$readonly."></td>".
				"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;:&nbsp;</td>".
				"<td><input type=\"text\" name=\"".$var."_i".$i."\" value=\"".$date[d]."\" ".$readonly."></td>".
				(($s)?"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;:&nbsp;</td>":"").
				(($s)?"<td><input type=\"text\" name=\"".$var."_s".$i."\" value=\"".$date[s]."\" style=\"color:#ccc;width:20px\"></td>":"").
			"</tr>".
		"</table>";
	}else{
		return "".
		"<table border=0 cellspacing=0 cellpadding=0>".
			"<tr>".
				"<td>".jp7_app_createSelect($var."_d".$i,"Dia","---",1,31,$date[d],$readonly)."</td>".
				"<td>&nbsp;/&nbsp;</td>".
				"<td>".jp7_app_createSelect($var."_m".$i,"Mês","---",1,12,$date[m],$readonly)."</td>".
				"<td>&nbsp;/&nbsp;</td>".
				"<td>".jp7_app_createSelect($var."_Y".$i,"Ano","---",date(Y)-100,date(Y)+20,$date[Y],$readonly)."</td>".
				"<td".(($time_xtra)?" ".$time_xtra:"").">&nbsp;-&nbsp;</td>".
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
	global $REMOTE_ADDR;
	global $jp7_app;
	if($jp7_app=="intermail"){
		global $s_intermail_cliente;
		global $s_intermail_user;
		$app_cliente=$s_intermail_cliente;
		$app_user=$s_intermail_user;
	}else{
		global $s_interadmin_cliente;
		global $s_interadmin_user;
		$app_cliente=$s_interadmin_cliente;
		$app_user=$s_interadmin_user;
	}
	$file_path="../../../".$jp7_app."/~".$app_cliente."/_log/";
	@chmod($file_path,0777);
	if(date(d,filemtime($file_path.$log.".log"))==date(d)){
		ob_start();
		readfile($file_path.$log.".log");
		$file_data=ob_get_contents();
		ob_end_clean();
	}
	$file=fopen($file_path.$log.".log","w");
	fwrite($file,$file_data.date("d/m H:i")." - ".$app_user." - ".$REMOTE_ADDR." - ".$S."\r\n");
	fclose($file);
	copy($file_path.$log.".log",$file_path.$log."_".date(d).".log");
}

// jp7_msg (2003/XX/XX)
function jp7_msg($S,$type){
	include "../../inc/msg.php";
}

// jp7_phpmyadmin_path (2004/06/23)
function jp7_phpmyadmin_path($path="../../_admin/phpmyadmin/",$i=0){
	if(is_dir($path)||$i>3)return $path;
	else return jp7_phpmyadmin_path("../".$path,$i++);
}

// jp7_phpmyadmin_aplicacao_path (2004/06/23)
function jp7_phpmyadmin_aplicacao_path($path="../../_admin/phpmyadmin/",$path2="../"){
	if(is_dir($path)||$i>3){
		global $SCRIPT_NAME;
		global $jp7_app;
		return $path2.((strpos($SCRIPT_NAME,$jp7_app)===false)?"../":$jp7_app."/");
	}
	else return jp7_phpmyadmin_aplicacao_path("../".$path,"../".$path2);
}
?>
