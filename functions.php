<?
// JP7's PHP Functions - JP7
// Copyright 2002-2006 JP7
// http://jp7.com.br
// Versão 1.0 - 2006/03/09


// JP7's PHP Functions
if(!@include "7.lib.php")if(!@include $DOCUMENT_ROOT."/inc/7.lib.php")if(!@include @ini_get('doc_root')."/inc/7.lib.php")echo "<span style=\"color:red\">7.lib not found</span>";
if(!$is->browser)$is=new Browser($HTTP_USER_AGENT);


// JP7

$iMenu=0;

function makeMenuItem($id,$title){
	global $dhtml;
	global $secao;
	global $iMenu;
	return "<a ".(($dhtml)?"onclick=\"go(".$iMenu.")\" style=\"cursor:hand\"":"href=\"../".$id."\"")." class=\"link-menu\"><span id=\"linkMenu_".$iMenu++."\" unselectable=\"on\">".(($id==$secao)?"<span class=\"font-verde\">":"").$title.(($id==$secao)?"</span>":"")."</span></a>";
}

$dhtml=($is->ie&&$is->v>5&&$is->win)?true:false;
?>
