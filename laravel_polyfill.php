<?php
// Temporary to use Laravel facades
class_alias('InterAdminLogFacade', 'Log');
class_alias('InterAdminStorage', 'Storage');
class_alias('InterAdminCacheFacade', 'Cache');
class_alias('InterAdminDBFacade', 'DB');

// Laravel polyfill
class App
{
    public static function environment($env)
    {
        return $env === getenv('APP_ENV');
    }
    public static function bound($interface)
    {
        return $interface === 'config';
    }
}

class Request
{
    public static function ip()
    {
        return array_get($_SERVER, 'REMOTE_ADDR');
    }
}

class Lang extends Illuminate\Support\Facades\Lang
{
    // Temporario para usar facade sem Laravel
    protected static function resolveFacadeInstance($name)
    {
        global $lang;
        static $root;
        if (!$root) {
            $root = new InterAdminLang($lang->lang === 'pt-br' ? 'pt-BR' : $lang->lang);
        }
        return $root;
    }
}

function base_path($path = '')
{
    return BASE_PATH.($path ? DIRECTORY_SEPARATOR.$path : $path);
}

// BUG: Won't work recursively, i.e: config files can't require other configs
function config($key)
{
    static $repository;
    if (!$repository) {
        $config = [];
        foreach (glob(base_path('config/*.php')) as $filename) {
            $config[basename($filename, '.php')] = require $filename;
        }
        $repository = new Illuminate\Config\Repository($config);
    }
    if (!isset($repository[$key])) {
        throw new OutOfBoundsException($key);
    }
    return $repository[$key];
}
