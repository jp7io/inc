<?php
// Include path para as classes do Cliente
set_include_path(jp7_path_find('../classes') . PATH_SEPARATOR .  get_include_path());

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
	if ($key == 'id' || substr($key, -3) === '_id') {
		if (strpos($key, 'openid_') !== false) {
			continue; // Conflito OpenID (openid_claimed_id)
		}
		if (is_string($value) && strpos($value, 'http://') !== false) {
			global $debugger;
			if ($debugger) {
				$debugger->sendTraceByEmail(new Exception('ID com URL'));
			}
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
	// Anti register-globals
	if (@ini_get('register_globals')) {
		unset($GLOBALS[$config->name_id]);
	}
	if (!is_array($_SESSION[$config->name_id]['interadmin'])) {
		$_SESSION[$config->name_id]['interadmin'] = array();
	}
	$s_session = &$_SESSION[$config->name_id]['interadmin'];
	$s_user = &$s_session['user'];
}
Zend_Registry::set('config', $config);
if (method_exists('Jp7_Bootstrap', 'initAdminBar')) {
	Jp7_Bootstrap::initAdminBar();
}

// PHPMyAdmin
if (strpos($_SERVER['PHP_SELF'], '_admin/phpmyadmin') === false && !$only_info) {
	require_once jp7_path_find('../inc/3thparty/adodb/adodb.inc.php');
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	$ADODB_LANG = 'pt-br';
	
	$dsn = jp7_formatDsn($config->db);
	/**
	 * @global ADOConnection $db
	 */
	$db = ADONewConnection($dsn);
	//$db->debug = true;
	
	if (!$db) {
		$config->db->pass = '{pass}';
		die(jp7_debug('Unable to connect to the database ' . jp7_formatDsn($config->db)));	
	}
	
	// Language
	$lang = null;
	if ($_GET['lang'] && is_string($_GET['lang'])) {
		if ($_GET['lang'] == $config->lang_default) {
			$lang = new jp7_lang($_GET['lang'], $_GET['lang']);
		} else {
			$columns = $db->MetaColumns($db_prefix . '_tipos');
			if ($columns['NOME_' . strtoupper($_GET['lang'])]) {
				$lang = new jp7_lang($_GET['lang'], $_GET['lang']);
			}
		}
	}
	if (!$lang) {
		$lang = new jp7_lang();
	}
	
	$config->lang = $config->langs[$lang->lang];
	// Compatibilidade temporária 
	if (!$c_site_title) {
		$c_site_title = $config->lang->title;
	}
	// Arquivos de idioma
	if (is_file($c_doc_root . '_default/inc/lang_' . $lang->lang . '.php')) {
		include $c_doc_root . '_default/inc/lang_' . $lang->lang . '.php';
	}
	if (is_file($c_doc_root . $config->name_id . '/inc/lang_' . $lang->lang . '.php')) {
		include $c_doc_root . $config->name_id . '/inc/lang_' . $lang->lang . '.php';
	}
		
	// Tipos (Navegação) (2007/05/16 by JP)
	if (!$wap) {
		$tipos = new interadmin_tipos($id_tipo, ($interadmin_tipos_noid) ? 0 : ($id) ? $id : $parent_id, true);
		$secao = toId($tipos->nome[0]);
		$secaoTitle = $tipos->nome[0];
		$subsecao = toId($tipos->nome[1]);
		$subsecaoTitle = $tipos->nome[1];
		if (!$seo) {
			if (InterAdminTipo::getDefaultClass() == 'InterAdminTipo' && class_exists(ucfirst($config->name_id) . '_InterAdminTipo')) {
				InterAdminTipo::setDefaultClass(ucfirst($config->name_id) . '_InterAdminTipo');
			}			
			$tipoObj = InterAdminTipo::getInstance($id_tipo);
			if ($id) {
				$interAdminObj = new InterAdmin($id);
			}
		}
	}
	
	// Login Check
	if ($tipos->restrito[1] || $tipos->restrito[$tipos->i-1]) {
		include '../../inc/login_check.php';
	}
	
	$config->build = interadmin_get_version($config->name_id, '{build}');
	$c_view = new Jp7_View();
	Zend_Registry::set('config', $config);
	// JavaScript
	$c_view->headScript()->appendFile($c_path_js . 'interdyn.js');
	$c_view->headScript()->appendFile($c_path_js . 'interdyn_checkflash.js');
	$c_view->headScript()->appendFile($c_path_js . 'interdyn_form.js');
	$c_view->headScript()->appendFile($c_path_js . 'interdyn_form_lang_' . $lang->lang . '.js');
	$c_view->headScript()->appendFile($c_path_js . 'swfobject' . ($c_swfobject ? '_' . $c_swfobject : '') . '.js');
	$c_view->headScript()->appendFile($c_path_js . 'jquery/jquery-1.3.2.min.js');
	if ($config->menu != 'none') {
		if (strpos($config->menu, '../') !== false) {
			$c_view->headScript()->appendFile($config->menu);
		} else {
			if ($config->menu) {
				$c_view->headScript()->appendFile($c_path_js . 'interdyn_menu_' . $config->menu . '.js');
			} else {
				$c_view->headScript()->appendFile('../../js/interdyn_menu_' . $config->name_id . '.js');
			}
			$c_view->headScript()->appendFile('../../js/interdyn_menu_str.php?lang=' . $lang->lang . ($interadmin_gerar_menu ? '&interadmin_gerar_menu=' . $interadmin_gerar_menu : ''));
		}
	}
	$c_view->headScript()->appendFile($c_path_js . 'interdyn_menu.js');
	$c_view->headScript()->appendFile('../../js/functions.js');
	$c_view->headScript()->appendFile('../../js/init.js');
	
	// CSS
	$c_view->headLink()->appendStylesheet($c_path_css . '7_w3c.css');
	$c_view->headLink()->appendStylesheet('../../css/' . $config->name_id . '.css');
	if ($c_template) {
		$c_view->headLink()->appendStylesheet('/_default/css/7_templates.css');
		$c_view->headLink()->appendStylesheet('/_templates/' . $c_template . '/css/style.css');
	}
}