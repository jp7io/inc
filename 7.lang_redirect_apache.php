<?php

$path = $_SERVER['SCRIPT_NAME'];
if (!$path) {
    $path = $_SERVER['REQUEST_URI'];
}
$dirname = dirname($path);
$dirname = mb_substr($dirname, strrpos($dirname, '/'));
$basename = basename($path);
if (strrpos($go_url, '.php') === false) {
    $go_url = jp7_path($go_url).'index.php';
}

if (file_exists('../../site/'.$go_url)) {
    include '../../site/'.$go_url;
} else {
    include '../../site/_go/index.php';
}
