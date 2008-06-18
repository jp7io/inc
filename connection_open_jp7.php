<?
if((strpos($HTTP_HOST,"jp7.com.br")!==false||strpos($HTTP_HOST,"convidar.com.br")!==false||$c_server_type=="Default"||$c_server_type=="Principal")&&$c_server_type!="JPSete"){
	// JP7 (LocaWeb)
	$c_server_type="Principal";
	$db_host="mysql01.jp7.com.br";
	if(!$db_name)$db_name="jp71";
	$db_user="jp71";
	$db_pass="hov572hov71";
	if(!isset($c_path))$c_path=$c_site."/";
	if(!$jp7_app)$publish=true;
}elseif(strpos($HTTP_HOST,"jpsete.com.br")!==false||$c_server_type=="JPSete"){
	// JP7 (JPSete)
	$c_server_type="Principal";
	$db_host="jpsete.com.br";
	if(!$db_name)$db_name="jpseteco_interadmin";
	$db_user="root";
	$db_pass="vox06";
	if(!isset($c_path))$c_path=$c_site."/";
	if(!$jp7_app)$publish=true;
}else{
	// JP7 (Local)
	$c_server_type="Local";
	$db_host=($db_type=="mssql")?"jp":"localhost";
	if(!$db_name)$db_name=(strpos($REQUEST_URI,"_outros")!==false)?"interadmin_outros":"interadmin";
	$db_user=($db_type=="mssql")?"sa":"root";
	$db_pass="123";
	if(!$c_path)$c_path=$c_site."/";
	if(!file_exists($c_doc_root.$c_path)){
		//echo "<pre>".dirname($SCRIPT_NAME);
		$path_size=explode("/",dirname($SCRIPT_NAME));
		//print_r($path_size)."\n";
		$path_arr=array_slice($path_size,1,count($path_size)-3);
		//print_r($path_arr)."\n";
		$c_path=implode("/",$path_arr)."/";
		//echo $c_path."\n";
		//echo "</pre>";
		//$c_path="_outros/".$c_path;		
	}
	//if(!$jp7_app)$publish=false;
	$googlemaps_key="ABQIAAAAsTwKY5N-m6Cx1Kj1a16NkxT3_qgDTySkwweE9jZ4ymNJXfPNXBRlNbYW-v3TBcZ8ELaBai9ocj8iAA";
}
$c_path_default="/_default/";
$c_path_js="/_default/js/";
$c_path_css="/_default/css/";
?>
