<?
if (!$db_host) {
	include $c_doc_root . 'inc/connection_open_jp7.php';
}

// Paths
$c_path = jp7_path($c_path);
$c_root = $c_doc_root . $c_path;
if (!$c_path_js) $c_path_js = '/_default/js/';
if (!$c_path_css) $c_path_css = '/_default/css/';
if (!$c_path_default) $c_path_default = '/_default/';
if (!$c_lang_default) $c_lang_default = 'pt-br';
$config->server->url = 'http://' . $HTTP_HOST . '/' . $c_path; // Temp - Delete it when InterSite Class get finished
$c_url = $config->server->url;

// Check IDs
foreach ($_REQUEST as $key => $value) {
	if ($key=='id' || strpos($key, '_id') !== false || strpos($key, 'id_') !== false) {
		if ($value && strpos($value, 'http://') !== false) {
			jp7_debug('ID com URL');
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $GLOBALS['c_path']);
			exit();
		}
	}
}

// Templates
if($c_template)include $c_doc_root . '_templates/' . $c_template . '/config.php';

// Session (Precisa para o Preview e pode ser usado para outros fins)
if (!session_id()) {
	session_name($c_site);
	session_start();
}

// PHPMyAdmin
if (strpos($_SERVER['PHP_SELF'], '_admin/phpmyadmin') === FALSE && !$only_info) {

// Language
$lang = ($_GET['lang'] && is_string($_GET['lang'])) ? new jp7_lang($_GET['lang'], $_GET['lang']) : new jp7_lang();
$config->lang = $config->langs[$lang->lang];
if  (!$c_site_title) $c_site_title = $config->lang->title;

@include $c_doc_root . '_default/inc/lang_' . $lang->lang . '.php';
@include $c_root . 'inc/lang_' . $lang->lang . '.php';

if (!$db_type) $db_type = 'mysql';
include jp7_path_find('../inc/3thparty/adodb/adodb.inc.php');
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
$ADODB_LANG = 'pt-br';
$dsn = "{$db_type}://{$db_user}:{$db_pass}@{$db_host}/{$db_name}"; 
$db = ADONewConnection($dsn);
//$db->debug = true;

// Tipos (Navegaчуo) (2007/05/16 by JP)
if (!$wap) {
	$tipos = new interadmin_tipos($id_tipo, ($interadmin_tipos_noid) ? 0 : ($id) ? $id : $parent_id, true);
	$secao = toId($tipos->nome[0]);
	$secaoTitle = $tipos->nome[0];
	$subsecao = toId($tipos->nome[1]);
	$subsecaoTitle = $tipos->nome[1];
	if ($c_site != 'ci' && $c_site != 'ciagt') $tipoObj = new InterAdminTipo($id_tipo);
}

// Login Check
if ($tipos->restrito[1] || $tipos->restrito[$tipos->i-1]) include '../../inc/login_check.php';

// /PHPMyAdmin
}
?>