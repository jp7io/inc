<?php
if ($seo_baseurl) {
	// Com SEO
	$baseHref = $seo_baseurl;
} else {
	$baseHref = 'http://' . $_SERVER['HTTP_HOST'];
	$uriSemQueryString = preg_replace('/([^?]*)(.*)/', '\1', $_SERVER['REQUEST_URI']);
	if (!$uriSemQueryString || $uriSemQueryString == '/' . $c_path) {
		// Home
		$baseHref .= '/' . $c_path . 'site/home/index.php';		
	} elseif ($lang->lang != $c_lang_default && $go_url) {
		// Língua
		$baseHref .= '/' . $c_path . $lang->path_url . $go_url;
	} else {
		$baseHref .= $uriSemQueryString;
	}
}

$c_build = interadmin_get_version($config->name_id, '{build}');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1" />
<meta name="language" content="<?= $lang->lang ?>" />
<meta name="description" content="<?= $config->lang->description ?>" />
<meta name="keywords" content="<?= $config->lang->keywords ?>" />
<meta name="copyright" content="<?=date(Y)?> <?= $config->copyright ?>" />
<meta name="robots" content="all" />
<meta name="author" content="JP7 - http://jp7.com.br<? if($c_parceiro) { ?> | <?= $c_parceiro ?><? } ?>" />
<meta name="generator" content="JP7 InterAdmin" />
<meta name="version" content="<?php echo @interadmin_get_version($config->name_id); ?>" />
<?php if ($config->ie7compatible) { ?>
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
<?php } ?>
<?php if ($config->google_site_verification) { ?>
	<meta name="google-site-verification" content="<?php echo $config->google_site_verification; ?>" />
<?php } ?>
<title><?= ($s_session['preview']) ? "PREVIEW | " : ""?><? if($p_title){ ?><?= $p_title ?><? }else{ ?><?= $c_site_title ?><? if($secao && $secao != "home"){ ?> | <?= $secaoTitle ?><? if ($subsecao && $subsecao != "home") { ?> | <?= $subsecaoTitle ?><? } ?><? } ?><? } ?></title>
<base href="<?php echo $baseHref; ?>" />
<link rel="stylesheet" type="text/css" href="<?= $c_path_css ?>7_w3c.css?build=<?php echo $c_build; ?>" />
<link rel="stylesheet" type="text/css" href="../../css/<?= $c_site ?>.css?build=<?php echo $c_build; ?>" />
<? if($c_template) { ?>
	<link rel="stylesheet" type="text/css" href="/_default/css/7_templates.css?build=<?php echo $c_build; ?>" />
	<link rel="stylesheet" type="text/css" href="/_templates/<?= $c_template ?>/css/style.css?build=<?php echo $c_build; ?>" />
<? } ?>
<script type="text/javascript">
var d=document
var site='<?= $c_site ?>'
var secao='<?= $secao ?>'
var subsecao='<?= $subsecao ?>'
var isPopup='<?= $isPopup ?>'
var noMenu='<?= $noMenu ?>'
var noSubMenu='<?= $noSubMenu ?>'
var isFrameset='<?= $isFrameset ?>'
var isFrame='<?= $isFrame ?>'

var DMquerystring=null
var tipo_id='<?= $id ?>'
var tipos=new Array(<? for($i = 0;$i<$tipos->i;$i++) { ?>'<?= $tipos->id_tipo[$i] ?>'<? if($i+1<$tipos->i) { ?>,<? } ?><? } ?>)

var path='<?= $c_path ?>'
var lang='<?= $lang->lang ?>'
var lang_path='<?= $lang->path_2 ?>'

var s_interadmin_preview='<?= $s_session['preview'] ?>'

var fullpath='http://'+location.host+'/'+path+lang_path

<? if ($s_session['preview'] && !$isPopup) { ?>
	//if(!parent.frames.length)location='http://'+location.host+'/'+path+'visualizar.php?redirect='+location.toString()
<? } ?>
</script>
<script type="text/javascript" src="<?= $c_path_js ?>interdyn.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>interdyn_checkflash.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>interdyn_form.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>interdyn_form_lang_<?= $lang->lang ?>.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>swfobject<? if ($c_swfobject) { ?>_<?= $c_swfobject ?><? } ?>.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>jquery/jquery-1.3.2.min.js?build=<?php echo $c_build; ?>"></script>
<? if ($c_menu != "none") { ?>
	<? if (strpos($c_menu, "../") !== false) { ?>
		<script type="text/javascript" src="<?= $c_menu ?>"></script>
	<? }else{ ?>
		<script type="text/javascript" src="<? if($c_menu) { ?><?= $c_path_js ?>interdyn_menu_<?= $c_menu ?><? }else{ ?>../../js/interdyn_menu_<?= $c_site ?><? } ?>.js?build=<?php echo $c_build; ?>"></script>
		<script type="text/javascript" src="../../js/interdyn_menu_str.php?build=<?php echo $c_build; ?>&lang=<?= $lang->lang ?><? if($interadmin_gerar_menu) { ?>&interadmin_gerar_menu=<?= $interadmin_gerar_menu ?><? } ?>"></script>
	<? } ?>
<? } ?>
<script type="text/javascript" src="<?= $c_path_js ?>interdyn_menu.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="../../js/functions.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="../../js/init.js?build=<?php echo $c_build; ?>"></script>
<? $debugger->setSafePoint(true); // Flag indicating that from this point the debugger can output data ?>