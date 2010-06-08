<?php
if (!$config) {
	die(jp7_debug('Configuration object not found: $config'));
}
if (!$config->db || !$config->db->type) {
	die(jp7_debug('Database not set in the $config object'));
}

// Paths
$c_root = $c_doc_root . $config->name_id . '/';

if (!$c_path_js) {
	$c_path_js = '/_default/js/';
}
if (!$c_path_css) {
	$c_path_css = '/_default/css/';
}
if (!$c_path_default) {
	$c_path_default = '/_default/';
}

// Check IDs
foreach ($_REQUEST as $key => $value) {
	if ($key == 'id' || strpos($key, '_id') !== false || strpos($key, 'id_') !== false) {
		if ($value && strpos($value, 'http://') !== false) {
			jp7_debug('ID com URL');
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $GLOBALS['c_path']);
			exit();
		}
	}
}

// Templates
if ($c_template) {
	include $c_doc_root . '_templates/' . $c_template . '/config.php';
}

// Session (Precisa para o Preview e pode ser usado para outros fins)
if (!session_id()) {
	session_start();
}
if (is_null($s_session)) {
	if (!is_array($_SESSION[$config->name_id]['interadmin'])) {
		$_SESSION[$config->name_id]['interadmin'] = array();
	}
	$s_session = &$_SESSION[$config->name_id]['interadmin'];
	$s_user = &$s_session['user'];
}

// PHPMyAdmin
if (strpos($_SERVER['PHP_SELF'], '_admin/phpmyadmin') === false && !$only_info) {
	// Language
	$lang = ($_GET['lang'] && is_string($_GET['lang'])) ? new jp7_lang($_GET['lang'], $_GET['lang']) : new jp7_lang();
	$config->lang = $config->langs[$lang->lang];
	// Compatibilidade temporária 
	if (!$c_site_title) {
		$c_site_title = $config->lang->title;
	}
	
	@include $c_doc_root . '_default/inc/lang_' . $lang->lang . '.php';
	@include $c_doc_root . $config->name_id . '/inc/lang_' . $lang->lang . '.php';
	
	require_once jp7_path_find('../inc/3thparty/adodb/adodb.inc.php');
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$ADODB_LANG = 'pt-br';
	$dsn = "{$config->db->type}://{$config->db->user}:{$config->db->pass}@{$config->db->host}/{$config->db->name}";
	if ($config->db->flags) {
		$dsn .= $config->db->flags;
	}
	/**
	 * @global ADOConnection $db
	 */
	$db = ADONewConnection($dsn);
	//$db->debug = true;
	
	// Tipos (Navegação) (2007/05/16 by JP)
	if (!$wap) {
		$tipos = new interadmin_tipos($id_tipo, ($interadmin_tipos_noid) ? 0 : ($id) ? $id : $parent_id, true);
		$secao = toId($tipos->nome[0]);
		$secaoTitle = $tipos->nome[0];
		$subsecao = toId($tipos->nome[1]);
		$subsecaoTitle = $tipos->nome[1];
		if (!$seo) {
			if (class_exists(ucfirst($config->name_id) . '_InterAdminTipo')) {
				$tipoObj = call_user_func(array(ucfirst($config->name_id) . '_InterAdminTipo', 'getInstance'), $id_tipo);
			} else {
				$tipoObj = InterAdminTipo::getInstance($id_tipo);
			}
			if ($id) {
				$interAdminObj = new InterAdmin($id);
			}
		}
	}
	
	// Login Check
	if ($tipos->restrito[1] || $tipos->restrito[$tipos->i-1]) {
		include '../../inc/login_check.php';
	}
}
