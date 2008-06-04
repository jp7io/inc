<?
// Paths
$c_path = jp7_path($c_path);
$c_root = $c_doc_root . $c_path;
if (!$c_path_js) $c_path_js = '/_default/js/';
if (!$c_path_css) $c_path_css = '/_default/css/';
if (!$c_path_default) $c_path_default = '/_default/';
$c_url = 'http://' . $HTTP_HOST. '/' .$c_path;

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

@session_start();

// PHPMyAdmin
if (strpos($_SERVER['PHP_SELF'], '_admin/phpmyadmin') === false && !$only_info) {

// Language
$lang = ($_GET['lang'] && is_string($_GET['lang'])) ? new jp7_lang($_GET['lang'], $_GET['lang']) : new jp7_lang();
@include $c_doc_root . 'inc/lang_' . $lang->lang . '.php';
@include $c_root . 'inc/lang_' . $lang->lang . '.php';
@include $c_doc_root . '_default/inc/lang_' . $lang->lang . '.php';

// DB Connection
//if (!$db_adodb && ($db_type == 'mysql' || $db_type == '')) {
if ($db_mysql) {
	//$db = mysql_connect($db_host, $db_user, $db_pass) or die ('Could not connect');
	@$db = mysql_connect($db_host, $db_user, $db_pass) or die (jp7_debug(mysql_error(), null, false));	
	mysql_select_db($db_name, $db);
	include (dirname(__FILE__) . '/3thparty/adodb/adodb.inc.php');
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$ADODB_LANG = 'pt-br';
	//$db_type - Ex.: odbc, mssql, mysql, etc.
	$dsn = "mysql://{$db_user}:{$db_pass}@{$db_host}/{$db_name}"; 
	$adodb = ADONewConnection($dsn);
} else {
	if(!$db_type)$db_type='mysql';
	include (dirname(__FILE__) . '/3thparty/adodb/adodb.inc.php');
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$ADODB_LANG = 'pt-br';
	//$db_type - Ex.: odbc, mssql, mysql, etc.
	$dsn = "{$db_type}://{$db_user}:{$db_pass}@{$db_host}/{$db_name}"; 
	$db = ADONewConnection($dsn);
	$adodb = $db;
	//$db->debug = true;
}

// Tipos (Navegação) (2007/05/16 by JP)
if (!$wap) {
	$tipos = new interadmin_tipos($id_tipo, ($interadmin_tipos_noid) ? 0 : ($id) ? $id : $parent_id, true);
	$secao = toId($tipos->nome[0]);
	$secaoTitle = $tipos->nome[0];
	$subsecao = toId($tipos->nome[1]);
	$subsecaoTitle = $tipos->nome[1];
}

// Login Check
if ($tipos->restrito[1] || $tipos->restrito[$tipos->i-1]) include '../../inc/login_check.php';

// /PHPMyAdmin
}

// Publish
include $c_doc_root . 'inc/7.publish_open.php';
?>
