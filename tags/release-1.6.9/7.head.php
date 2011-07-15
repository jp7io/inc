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
	} elseif ($lang->lang != $config->lang_default && $go_url) {
		// Língua
		$baseHref .= '/' . $c_path . $lang->path_url . $go_url;
	} else {
		// Base href ficava incorreto quando haviam duas barras no endereço
		$baseHref .= str_replace('//', '/', $uriSemQueryString);
	}
}

$c_build = interadmin_get_version($config->name_id, '{build}');
?>
<?php if ($config->html5) { ?>
<!DOCTYPE HTML>
<?php } else { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php } ?>
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
<title><?php echo ($s_session['preview']) ? 'PREVIEW | ' : ''; ?><?php if ($p_title) { ?><?php echo $p_title; ?><?php } else { ?><?php echo $config->lang->title; ?><?php if ($secao && $secao != 'home') { ?> | <?php echo $secaoTitle; ?><?php if ($subsecao && $subsecao != 'home') { ?> | <?php echo $subsecaoTitle; ?><?php } ?><?php } ?><?php } ?></title>
<base href="<?php echo $baseHref; ?>" />
<link rel="stylesheet" type="text/css" href="<?php echo $c_path_css; ?>7_w3c.css?build=<?php echo $c_build; ?>" />
<link rel="stylesheet" type="text/css" href="../../css/<?php echo $config->name_id; ?>.css?build=<?php echo $c_build; ?>" />
<?php if($c_template) { ?>
	<link rel="stylesheet" type="text/css" href="/_default/css/7_templates.css?build=<?php echo $c_build; ?>" />
	<link rel="stylesheet" type="text/css" href="/_templates/<?php echo $c_template; ?>/css/style.css?build=<?php echo $c_build; ?>" />
<?php } ?>
<script type="text/javascript">
var d = document;
var site = '<?php echo $config->name_id; ?>';
var secao = '<?php echo $secao; ?>';
var subsecao = '<?php echo $subsecao; ?>';
var isPopup = '<?php echo $isPopup; ?>';
var noMenu = '<?php echo $noMenu; ?>';
var noSubMenu = '<?php echo $noSubMenu; ?>';
var isFrameset = '<?php echo $isFrameset; ?>';
var isFrame = '<?php echo $isFrame; ?>';

var DMquerystring = null;
var tipo_id = '<?php echo $id; ?>';
var tipos = new Array(<?php for($i = 0; $i < $tipos->i; $i++) { ?>'<?php echo $tipos->id_tipo[$i]; ?>'<?php if ($i + 1 < $tipos->i) { ?>,<?php } ?><?php } ?>);

var path = '<?php echo $c_path; ?>'
var lang = '<?php echo $lang->lang; ?>'
var lang_path = '<?php echo $lang->path_2; ?>'

var s_interadmin_preview = '<?php echo $s_session['preview']; ?>'

var fullpath = 'http://'+location.host+'/'+path+lang_path

<?php if ($s_session['preview'] && !$isPopup) { ?>
	//if(!parent.frames.length)location='http://'+location.host+'/'+path+'visualizar.php?redirect='+location.toString()
<?php } ?>
</script>
<script type="text/javascript" src="<?= $c_path_js ?>interdyn.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>interdyn_checkflash.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>interdyn_form.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>interdyn_form_lang_<?= $lang->lang ?>.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>swfobject<? if ($c_swfobject) { ?>_<?= $c_swfobject ?><? } ?>.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="<?= $c_path_js ?>jquery/jquery-1.3.2.min.js?build=<?php echo $c_build; ?>"></script>
<?php if ($config->menu != 'none') { ?>
	<?php if (strpos($config->menu, '../') !== false) { ?>
		<script type="text/javascript" src="<?php echo $config->menu; ?>"></script>
	<?php } else { ?>
		<script type="text/javascript" src="<?php if ($config->menu) { ?><?php echo $c_path_js; ?>interdyn_menu_<?php echo $config->menu; ?><?php } else { ?>../../js/interdyn_menu_<?php echo $config->name_id; ?><?php } ?>.js?build=<?php echo $c_build; ?>"></script>
		<script type="text/javascript" src="../../js/interdyn_menu_str.php?build=<?php echo $c_build; ?>&lang=<?php echo $lang->lang; ?><?php if ($interadmin_gerar_menu) { ?>&interadmin_gerar_menu=<?php echo $interadmin_gerar_menu; ?><?php } ?>"></script>
	<?php } ?>
<?php } ?>
<script type="text/javascript" src="<?php echo $c_path_js; ?>interdyn_menu.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="../../js/functions.js?build=<?php echo $c_build; ?>"></script>
<script type="text/javascript" src="../../js/init.js?build=<?php echo $c_build; ?>"></script>
<?php
// Flag indicating that from this point the debugger can output data
$debugger->setSafePoint(true);
?>