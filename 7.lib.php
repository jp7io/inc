<?php

/**
 * JP7's PHP Functions.
 *
 * Contains the main custom functions and classes
 *
 * @author JP7
 * @copyright Copyright 2002-2008 JP7 (http://jp7.com.br)
 *
 * @version 1.10 (2008/06/16)
 *
 * @category JP7
 */

/**
 * In case $_SERVER['SERVER_ADDR'] is not set, it gets the value from $_SERVER['LOCAL_ADDR'], needed on some Windows servers.
 */
if (empty($_SERVER['SERVER_ADDR']) && isset($_SERVER['LOCAL_ADDR'])) {
    $_SERVER['SERVER_ADDR'] = $_SERVER['LOCAL_ADDR'];
}
/**
 * In case $_SERVER['REMOTE_ADDR'] is not set, it gets the value from $_SERVER['REMOTE_HOST'], needed on some Windows servers.
 */
if (empty($_SERVER['REMOTE_ADDR']) && isset($_SERVER['REMOTE_HOST'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_HOST'];
}
// PHP FPM - use FastCgiExternalServer ... --pass-header Authorization
if (empty($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['Authorization'])) {
    list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', base64_decode(substr($_SERVER['Authorization'], 6)));
}

require __DIR__.'/7.functions.php';
require __DIR__.'/laravel_polyfill.php';

/**
 * @global bool $c_jp7
 * @deprecated
 */
global $c_jp7;
$c_jp7 = false;

error_reporting(E_ALL ^ E_NOTICE);

mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
setlocale(LC_CTYPE, ['pt_BR.ISO8859-1', 'pt_BR', 'Portuguese_Brazil']);
setlocale(LC_COLLATE, ['pt_BR.ISO8859-1', 'pt_BR', 'Portuguese_Brazil']);

date_default_timezone_set('America/Sao_Paulo');

if (!@ini_get('allow_url_fopen')) {
    @ini_set('allow_url_fopen', '1');
}
jp7_register_globals();

/**
 * @global Jp7_Debugger $debugger
 */
global $debugger;
// Jp7_Debugger lives in jp7io/classes-deprecated. Tenants that still require the package
// keep the real debug toolbar; once it is removed (interadmin Step 4) fall back to a no-op
// stand-in so boot does not fatal. showFilename() passes the path through unchanged
// (see jp7_load_partial / interadmin_arquivo in 7.functions.php).
$debugger = class_exists('Jp7_Debugger') ? new Jp7_Debugger() : new class {
    public function showSql($sql, $expanded = false) {}
    public function showFilename($file) { return $file; }
    public function setSafePoint($bool = true) {}
    public function showToolbar() {}
    public function __call($name, $arguments) { return $arguments[0] ?? null; }
};

/*
 * @global Browser $is
 */
global $is;
define('JP7_IS_WINDOWS', jp7_is_windows());
// Browser (user-agent sniffer) lives in jp7io/classes-deprecated. Keep it for tenants that
// still require the package; otherwise (interadmin Step 4) use a null-object exposing the
// same public surface so legacy reads (e.g. $is->ch) resolve to null instead of fataling.
$is = class_exists('Browser')
    ? new Browser($_SERVER['HTTP_USER_AGENT'] ?? '')
    : new class ($_SERVER['HTTP_USER_AGENT'] ?? '') {
        public $userAgent;
        public $browser = '';
        public $v = -1;
        public $os = '';
        public $robot = '';
        public function __construct($userAgent) { $this->userAgent = $userAgent; }
        public function __get($name) { return null; }
    };

register_shutdown_function('jp7_check_shutdown');
// Convert errors to Exceptions - Code taken from Laravel
set_error_handler(function ($level, $message, $file = '', $line = 0, $context = []) {
    if (error_reporting() & $level) {
        throw new ErrorException($message, 0, $level, $file, $line);
    }
});

// Fix permissions for created files
umask(0002);

/**
 * class jp7_db_pages.
 *
 * @version (2007/02/22)
 *
 * @deprecated Kept as an alias to Pagination class.
 */
if (class_exists('Pagination')) {
    class_alias('Pagination', 'jp7_db_pages');
}

// ORM settings for compatibility with old code. The right-hand classes-deprecated names
// survive for tenants that still require the package; interadmin (Step 4) resolves the
// aliases to their modern replacements instead so the legacy global names keep working.
class_alias(class_exists('Jp7_Date') ? 'Jp7_Date' : \Carbon\Carbon::class, 'Date');
if (class_exists('InterAdminRecordUrl')) {
    class_alias('InterAdminRecordUrl', 'RecordUrl');
}
class_alias('InterAdminTipo', 'Type');
class_alias('InterAdmin', 'Record');
class_alias(class_exists('InterAdminFieldFile') ? 'InterAdminFieldFile' : \Jp7\Interadmin\Field\FileField::class, 'FileField');

InterAdminTipo::setDefaultClass('InterAdminTipo');
Jp7\Interadmin\DynamicLoader::register();
Jp7\Laravel\CacheExtension::apply();