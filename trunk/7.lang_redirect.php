<?
$path=$SCRIPT_NAME;
if(!$path)$path=$REQUEST_URI;
$dirname=dirname($path);
$dirname=substr($dirname,strrpos($dirname,"/"));
$basename=basename($path);
include "../../site".$dirname."/".$basename;
?>
