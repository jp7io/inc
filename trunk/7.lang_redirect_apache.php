<?
$path=$SCRIPT_NAME;
if(!$path)$path=$REQUEST_URI;
$dirname=dirname($path);
$dirname=substr($dirname,strrpos($dirname,"/"));
$basename=basename($path);
if(strrpos($go_url,".php")===false){
	if(strrpos($go_url,"/index.php")===false){
		$go_url.="/index.php";
	}elseif(strrpos($go_url,"index.php")===false){
		$go_url.="index.php";
	}
}

if(file_exists("../../site/".$go_url)){
	include "../../site/".$go_url;
}else{
	include "../../site/_go/index.php";
}
?>
