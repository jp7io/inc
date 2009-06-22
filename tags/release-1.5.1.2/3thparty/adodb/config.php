<?
$c_cliente_title="CICESP";
$c_cliente_domains[0]="cicesp.org";

/*
$c_lang[0][0]="pt-br";
$c_lang[0][1]="Português";
$c_lang[1][0]="en";
$c_lang[1][1]="English";
*/

$c_menu_item[0][0]["label"]="Cadastro";
$c_menu_item[0][0]["action"]="";
$c_menu_item[0][1]["label"]="Pessoa Física";
$c_menu_item[0][1]["action"]="aplicacao_changeURL(\'../../~cicesp/aplicacao/cadastro.php?cadastro_tipo=pf\',\'Pessoa Física\')";
$c_menu_item[0][2]["label"]="Pessoa Jurídica";
$c_menu_item[0][2]["action"]="aplicacao_changeURL(\'../../~cicesp/aplicacao/cadastro.php?cadastro_tipo=pj\',\'Pessoa Jurídica\')";

$c_url_zoom=false;

$c_menu_manual=true;
$c_paginas_tamanho=200;
$c_publish=true;

// Álbuns

$c_url_2=true;

$c_url_2_img="auto";
$c_url_2_img_w=400;
$c_url_2_img_h=300;
$c_url_2_img_q=90;
$c_url_2_img_s=0;

$c_url_2_thumb="auto";
$c_url_2_thumb_w=70;
$c_url_2_thumb_h=70;
$c_url_2_thumb_q=85;
$c_url_2_thumb_s=0;

// Hosting
if(strpos($HTTP_HOST,$c_cliente_domains[0])!==false||strpos($HTTP_HOST,"jp7.com.br")!==false){
	// CICESP (LocaWeb)
	$c_server_type="Principal";
	$db_host="mysql.cicesp.org";
	$db_name="cicesp2";
	$db_user="cicesp2";
	$db_pass="cic4i9z3w5";
	$c_cliente_url="http://www.cicesp.org";
	$c_remote=true;
}
?>
