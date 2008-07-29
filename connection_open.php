<?
// Redirect
if(strpos($HTTP_HOST,"jp7.com.br")!==false&&strpos($HTTP_HOST,"www.jp7.com.br")===false){
	header("Location: http://www.jp7.com.br".$REQUEST_URI);
	exit();
}

// Session
session_start();

// Config
//error_reporting(E_ALL ^ E_NOTICE);
if(!setlocale(LC_CTYPE,"pt_BR"))setlocale(LC_CTYPE,"pt_BR.ISO8859-1");

// Info
$c_site="jp7";
$c_site_title="JP7";
$c_menu="none";
$c_publish=false;
$c_w3c=true;
$c_path="";
$c_doc_root=jp7_doc_root();
$db_prefix="interadmin_".$c_site;


// galeria
$c_arr_conf_gal=array();
$c_arr_conf_gal['bgImage']='../../img/interna/bg_tit_galeria.gif';
$c_arr_conf_gal['windowWidth']=980;
//$c_arr_conf_gal['windowHeight']=520;

// Servers
$c_servers_array=Array("jp7.com.br","www.jp7.com.br");
if(strpos($_SERVER['REQUEST_URI'],"/qa/")!==false){
	$c_email_oportunidades="debug@sites.jp7.com.br";
}elseif(in_array($HTTP_HOST,$c_servers_array)){
	$c_email_oportunidades="rh@jp7.com.br";
}else{
	$c_email_oportunidades="debug+oportunidades@sites.jp7.com.br";
}
include $c_doc_root."inc/connection_open_jp7.php";
include jp7_path_find("inc/7.connection_open.php");
?>