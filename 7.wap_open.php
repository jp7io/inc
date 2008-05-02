<?
if(strpos($HTTP_ACCEPT,"application/xhtml")!==false)$html=true;

if($html)header("content-type:text/html");
else header("content-type:text/vnd.wap.wml");

$wap_path="http://".$HTTP_HOST."/".$c_path."wap/";
$wap_path_img="http://".$HTTP_HOST."/".$c_path."img/wap/";

function wap_toHTML($S){
	global $html;
	if(!$html)$S=str_replace("$","$$",$S);
	$S=str_replace("<br>","<br/>",$S);
	$S=preg_replace("([áàãâä])","a",$S);
	$S=preg_replace("([éèêë&])","e",$S);
	$S=preg_replace("([íìîï])","i",$S);
	$S=preg_replace("([óòõôö])","o",$S);
	$S=preg_replace("([úùûü])","u",$S);
	$S=preg_replace("([ç])","c",$S);
	$S=preg_replace("([ñ])","n",$S);
	$S=preg_replace("([ÁÀÃÂÄ])","A",$S);
	$S=preg_replace("([ÉÈÊË&])","E",$S);
	$S=preg_replace("([ÍÌÎÏ])","I",$S);
	$S=preg_replace("([ÓÒÕÔÖ])","O",$S);
	$S=preg_replace("([ÚÙÛÜ])","U",$S);
	$S=preg_replace("([Ç])","C",$S);
	$S=preg_replace("([Ñ])","N",$S);
	return $S;
}

echo "<?xml version=\"1.0\"?>";

// Styles
if(!$wap_style_bg)$wap_style_link="#FFFFFF";
if(!$wap_style_text)$wap_style_link="#000000";
if(!$wap_style_link)$wap_style_link="#0000FF";
?>

<?if($html){?>
	<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
	<html>
	<head>
	<title><?=$c_site_title?><?=($secaoTitle!="Home")?" - ".$secaoTitle:""?></title>
	<style>
	a:link,
	a:active,
	a:visited{color:<?=$wap_style_link?>}
	</style>
	</head>
	<body bgcolor="<?=$wap_style_bg?>" text="<?=$wap_style_text?>" link="<?=$wap_style_link?>" vlink="<?=$wap_style_link?>" alink="<?=$wap_style_link?>">
	<table border=0 cellpadding=5><tr><td>
<?}else{?>
	<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.2//EN" "http://www.wapforum.org/DTD/wml_1.2.xml">
	<wml>
	<card id="<?=toId($secaoTitle)?>_<?=toId($subsecaoTitle)?>" title="<?=$c_site_title?><?=($secaoTitle!="Home")?" - ".$secaoTitle:""?>">
	<?if($secaoTitle!="Home"||$subsecaoTitle!="Home"){?>
		<p><small>
	<?}?>
<?}?>
