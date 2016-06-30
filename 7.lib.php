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

/**
 * @global bool $c_jp7
 * @deprecated
 */
global $c_jp7;
$c_jp7 = false;
// Security: don't leak debug variables
if (getenv('APP_DEBUG') && getenv('APP_ENV') !== 'local') {
    if ($_SERVER['REMOTE_ADDR'] !== gethostbyname('office.jp7.com.br')) {
        putenv('APP_DEBUG=');
    }
}

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
$debugger = new Jp7_Debugger();

/*
 * @global Browser $is
 */
global $is;
define('JP7_IS_WINDOWS', jp7_is_windows());
$is = new Browser($_SERVER['HTTP_USER_AGENT']);

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
class_alias('Pagination', 'jp7_db_pages');

// Temporary to use Laravel facades
class_alias('InterAdminLogFacade', 'Log');
class_alias('InterAdminStorage', 'Storage');
class_alias('InterAdminCacheFacade', 'Cache');
class_alias('InterAdminDBFacade', 'DB');
