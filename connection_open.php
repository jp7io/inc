<?
// Session
//session_start();

// Config
//error_reporting(E_ALL);
if(!setlocale(LC_CTYPE,"pt_BR"))setlocale(LC_CTYPE,"pt_BR.ISO8859-1");

// Info
$c_site="jp7";
$c_site_title="JP7";
$c_publish=true;
$c_doc_root=jp7_doc_root();
$db_prefix="interadmin_".$c_site;

// Servers - JP7
@include $c_doc_root."inc/connection_open_jp7.php";
if($c_server_type!="Local")$db_name="jp71";

include jp7_path_find("inc/7.connection_open.php");
?>
