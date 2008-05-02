<?
$secoes=array(
	"Serviços",
	"Produtos",
	"Cases",
	"Clientes",
	"Contato"
)
?>
<title>JP7<?if($secao!='home'){?><?}?></title>
<meta name="description" content="Desenvolvimento e Consultoria para Internet">
<meta name="keywords" content="jp7,internet,desenvolvimento,consultoria,interdyn,interadmin,intermail,jp,joao pedro,barbosa,ricardo pivetta,pivetta,e-mail marketing">
<meta name="copyright" content="Copyright 2002-<?=date("Y")?> JP7">
<meta name="author" content="JP7 - http://jp7.com.br">
<meta name="generator" content="JP7's InterAdmin">
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<?if(strpos("site/",$page)===false){?><base href="http://<?=$HTTP_HOST?><?=$SCRIPT_NAME?>"><?}?>
<link rel="stylesheet" href="../../css/jp7.css">
<?if($is->ns4&&$is->win){?><link rel="stylesheet" href="../../css/jp7_ns.css"><?}?>
<?if($dhtml){?><style>body{overflow:hidden}</style><?}?>
<script>
var d=document
var site='jp7'
var secao='<?=$secao?>'
var subsecao='<?=$subsecao?>'
<?if($dhtml&&$secao&&$secao!="home"&&$site!="extranet"&&!$is_popup){?>
	if(!parent.frames.length)location='../home/index.php?go=<?=$secao?>'
<?}?>
</script>
<script src="<?=$c_path_js?>interdyn.js"></script>
<script src="<?=$c_path_js?>interdyn_form.js"></script>
<script src="<?=$c_path_js?>interdyn_color.js"></script>
<script src="<?=$c_path_js?>interdyn_fadelink.js"></script>
<script>
function initDefault(){
	if(isDef('init'))init()
	if(is.ie){
		newFadeLinks('link-menu')
		initFadeLinks()
	}
}
onload=initDefault
</script>
