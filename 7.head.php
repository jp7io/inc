<?php
// Mantendo B.C. (Compatibilidade Reversa)
$c_build = $config->build;

if ($seo_baseurl) {
    // Com SEO
    $baseHref = $seo_baseurl;
} else {
    $baseHref = ($_SERVER['HTTPS'] ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'];
    $uriSemQueryString = preg_replace('/([^?]*)(.*)/', '\1', $_SERVER['REQUEST_URI']);
    if (!$uriSemQueryString || $uriSemQueryString == '/'.$c_path) {
        // Home
        $baseHref .= '/'.$c_path.'site/home/index.php';
    } elseif ($lang->lang != $config->lang_default && $go_url) {
        // Língua
        $baseHref .= '/'.$c_path.$lang->path_url.$go_url;
    } else {
        // Base href ficava incorreto quando haviam duas barras no endereço
        $baseHref .= str_replace('//', '/', $uriSemQueryString);
    }
}
?>
<?php if ($config->html5) {
    ?>
<!DOCTYPE HTML>
<?php 
} else {
    ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php 
} ?>
<html xmlns="http://www.w3.org/1999/xhtml"<?php echo $config->facebook_namespace ? ' xmlns:fb="http://www.facebook.com/2008/fbml"' : ''; ?>>
<head>
<meta http-equiv="content-type" content="text/html;charset=<?php echo $config->charset ?>" />
<meta name="language" content="<?php echo $lang->lang; ?>" />
<meta name="description" content="<?php echo $config->lang->description; ?>" />
<meta name="keywords" content="<?php echo $config->lang->keywords; ?>" />
<meta name="copyright" content="<?php echo date('Y'); ?> <?php echo $config->copyright; ?>" />
<meta name="robots" content="all" />
<meta name="author" content="JP7 - http://jp7.com.br<?php if ($c_parceiro) {
    ?> | <?php echo $c_parceiro;
    ?><?php 
} ?>" />
<meta name="generator" content="JP7 InterAdmin" />
<meta name="version" content="<?php echo interadmin_get_version(); ?>" />
<?php if ($config->ie8compatible) {
    ?>
	<meta http-equiv="X-UA-Compatible" content="IE=8" />
<?php 
} elseif ($config->ie7compatible) {
    ?>
	<meta http-equiv="X-UA-Compatible" content="IE=7" />
<?php 
} ?>
<?php if ($config->google_site_verification) {
    ?>
	<meta name="google-site-verification" content="<?php echo $config->google_site_verification;
    ?>" />
<?php 
} ?>
<title><?php echo($s_session['preview']) ? 'PREVIEW | ' : ''; ?><?php if ($p_title) {
    ?><?php echo $p_title;
    ?><?php 
} else {
    ?><?php echo $config->lang->title;
    ?><?php if ($secao && $secao != 'home') {
    ?> | <?php echo $secaoTitle;
    ?><?php if ($subsecao && $subsecao != 'home') {
    ?> | <?php echo $subsecaoTitle;
    ?><?php 
}
    ?><?php 
}
    ?><?php 
} ?></title>
<base href="<?php echo $baseHref; ?>" />
<?php if ($c_view instanceof Zend_View) {
    ?>
	<?php echo $c_view->headLink();
    ?>
	<?php echo $c_view->headScript();
    ?>
<?php 
} ?>
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
var tipos = new Array(<?php for ($i = 0; $i < $tipos->i; $i++) {
    ?>'<?php echo $tipos->id_tipo[$i];
    ?>'<?php if ($i + 1 < $tipos->i) {
    ?>,<?php 
}
    ?><?php 
} ?>);

var path = '<?php echo $c_path; ?>'
var lang = '<?php echo $lang->lang; ?>'
var lang_path = '<?php echo $lang->path_2; ?>'

var s_interadmin_preview = '<?php echo $s_session['preview']; ?>'

var fullpath = '<?php echo($_SERVER['HTTPS'] ? 'https' : 'http'); ?>://'+location.host+'/'+path+lang_path

<?php if ($s_session['preview'] && !$isPopup) {
    ?>
	//if(!parent.frames.length)location='http://'+location.host+'/'+path+'visualizar.php?redirect='+location.toString()
<?php 
} ?>
</script>
<?php
// Flag indicating that from this point the debugger can output data
$debugger->setSafePoint(true);
?>