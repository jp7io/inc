<?
// Path
$redirect_path=$SCRIPT_NAME;
if(!$redirect_path)$redirect_path=$REQUEST_URI;

if($id_tipo){
	
	// toId_2 (2004/01/18)
	function toId_2($S){
		$S=preg_replace("([áàãâäÁÀÃÂÄª])","a",$S);
		$S=preg_replace("([éèêëÉÈÊË&])","e",$S);
		$S=preg_replace("([íìîïÍÌÎÏ])","i",$S);
		$S=preg_replace("([óòõôöÓÒÕÔÖº])","o",$S);
		$S=preg_replace("([úùûüÚÙÛÜ])","u",$S);
		$S=preg_replace("([çÇ])","c",$S);
		$S=preg_replace("([ñÑ])","n",$S);
		$S=preg_replace("([^(\d\w)])","",$S);
		$S=preg_replace("([\(\)])","",$S);
		$S=strtolower($S);
		return $S;
	}
	
	// Open
	include "../../inc/connection_open_light.php";
	
	// Selects
	$sql = "SELECT parent_id_tipo,nome,nome_en FROM ".$db_prefix."_tipos WHERE id_tipo=".$id_tipo;
	$rs = mysql_query($sql,$db)or die(mysql_error());
	if ($row=mysql_fetch_object($rs)){
		$parent_id_tipo=$row->parent_id_tipo;
		if($parent_id_tipo){
			$basename=toId_2($row->nome).".php";
			$basename_en=toId_2($row->nome_en).".php";
			$sql2 = "SELECT parent_id_tipo,nome,nome_en FROM ".$db_prefix."_tipos WHERE id_tipo=".$parent_id_tipo;
			$rs2=mysql_query($sql2,$db)or die(mysql_error());
			if ($row2=mysql_fetch_object($rs2)){
				$dirname="/".toId_2($row2->nome);
				$dirname_en="/".toId_2($row2->nome_en);
			}
			mysql_free_result($rs2);
		}
		else{
			$basename="index.php";
			$basename_en="index.php";
			$dirname="/".toId_2($row->nome);
			$dirname_en="/".toId_2($row->nome_en);
		}
	}
	mysql_free_result($rs);
	
	// Close
	include "../../inc/connection_close.php";
	
}else{
	$dirname=dirname($redirect_path);
	$dirname=substr($dirname,strrpos($dirname,"/"));
	$basename=basename($redirect_path);
}

// Redirect/Include
$redirect_target="../../site".$dirname."/".$basename;
if(strpos($redirect_path,"/site/")!==false)header("Location: ".$redirect_target);
else include $redirect_target;
?>
