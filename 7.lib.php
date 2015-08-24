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
 * Checks for Fatal Error preventing White Screen of Death.
 */
function jp7_check_shutdown()
{
    $lasterror = error_get_last();
    switch ($lasterror['type']) {  // Is it a Fatal Error?
        case E_ERROR:
        case E_USER_ERROR:
        case E_RECOVERABLE_ERROR:
            global $debugger;
            if ($debugger) {
                // Nesse ponto as exceções não podem mais ser tratadas
                $debugger->setExceptionsEnabled(false);
            }
            die(jp7_debug($lasterror['message'].' in <b>'.$lasterror['file'].'</b> on line '.$lasterror['line']));
            break;
    }
}
/**
 * Checks for uncaught exceptions, preventing White Screen of Death.
 */
function jp7_check_exception($e)
{
    global $debugger;
    if ($debugger) {
        // Nesse ponto as exceções não podem mais ser tratadas
        $debugger->setExceptionsEnabled(false);
    }
    die(jp7_debug('Uncaught <b>'.get_class($e).'</b> with message <b>'.$e->getMessage().'</b> in '.$e->getFile().' on line '.$e->getLine(), null, $e->getTrace()));
}

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

require __DIR__.'/7.functions.php';

/**
 * @global bool $c_jp7
 */
global $c_jp7;
$c_jp7 = false;
global $c_development;
$c_development = $_SERVER['HTTP_HOST'] == 'localhost' || 
    $_SERVER['SERVER_ADDR'] == '127.0.0.1' || 
    $_SERVER['SERVER_ADDR'] == '::1' || 
    startsWith('192.168.0.', $_SERVER['REMOTE_ADDR']);
if ($c_development) {
    $c_jp7 = true;
} elseif (in_array(mb_substr($_SERVER['REMOTE_ADDR'], 0, 4), array('179.', '177.', '178.'))) {
    $c_jp7 = ($_SERVER['REMOTE_ADDR'] == gethostbyname('office.jp7.com.br'));
}

if ($c_jp7) {
    if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
        error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
    } elseif (version_compare(PHP_VERSION, '5.3.0') >= 0) {
        error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
    } else {
        error_reporting(E_ALL ^ E_NOTICE);
    }
} else {
    error_reporting(0);
}

mb_internal_encoding('UTF-8');
mb_regex_encoding('UTF-8');
setlocale(LC_CTYPE, array('pt_BR.ISO8859-1', 'pt_BR', 'Portuguese_Brazil'));
setlocale(LC_COLLATE, array('pt_BR.ISO8859-1', 'pt_BR', 'Portuguese_Brazil'));

date_default_timezone_set('America/Sao_Paulo');

if (!@ini_get('allow_url_fopen')) {
    @ini_set('allow_url_fopen', '1');
}
jp7_register_globals();

// TODO - Remove ROOT_PATH
defined('ROOT_PATH') || define('ROOT_PATH', dirname(dirname(__FILE__)));

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

/*
 * Define o diretório com os arquivos do Krumo
 */
define('KRUMO_DIR', dirname(__FILE__).'/../_default/js/krumo/');

if ($c_development) {
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();
} else {
    set_error_handler(array($debugger, 'errorHandler'));
    register_shutdown_function('jp7_check_shutdown');
    set_exception_handler('jp7_check_exception');
}

// Fix permissions for created files
umask(0002);

/**
 * class jp7_db_pages.
 *
 * @version (2007/02/22)
 *
 * @deprecated Kept as an alias to Pagination class.
 */
class jp7_db_pages extends Pagination
{
    // Alterado o nome para Pagination
}

function jp7_package_path($package)
{
    $pattern = '/^(inc|classes)/';
    if (!preg_match($pattern, $package)) {
        throw new InvalidArgumentException('Package does no match '.$pattern);
    }
    return __DIR__ . '/../' . $package;
}
