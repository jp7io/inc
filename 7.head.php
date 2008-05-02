<meta name="author" content="JP7 - http://jp7.com.br<?if($c_parceiro){?> | <?=$c_parceiro?><?}?>" />
<meta name="generator" content="JP7 InterAdmin" />
<title><?=($s_interadmin_preview)?"PREVIEW | ":""?><?if($p_title){?><?=$p_title?><?}else{?><?=$c_site_title?><?if($secao&&$secao!="home"){?> | <?=$secaoTitle?><?if($subsecao&&$subsecao!="home"){?> | <?=$subsecaoTitle?><?}?><?}?><?}?></title>
<base href="http://<?=$HTTP_HOST?><?=($REQUEST_URI&&!$baseurl)?$REQUEST_URI:$SCRIPT_NAME?>" />
<link rel="stylesheet" type="text/css" href="<?=$c_path_css?>7<?if($c_w3c){?>_w3c<?}?>.css" />
<link rel="stylesheet" type="text/css" href="../../css/<?=$c_site?>.css" />
<?if($c_template){?>
	<link rel="stylesheet" type="text/css" href="/_templates/<?=$c_template?>/css/style.css" />
<?}?>
<?if($debug_filename){?>
	<style>div.filename{display:block}</style>
<?}?>
<?if(!$c_w3c){?><script type="text/javascript">if(document.getElementById&&!document.all)document.write('<link rel="stylesheet" href="../../css/<?=$c_site?>_ns.css">')</script><?}?>
<script type="text/javascript">
var d=document
var site='<?=$c_site?>'
var secao='<?=$secao?>'
var subsecao='<?=$subsecao?>'
var isPopup='<?=$isPopup?>'
var noMenu='<?=$noMenu?>'
var noSubMenu='<?=$noSubMenu?>'
var isFrameset='<?=$isFrameset?>'
var isFrame='<?=$isFrame?>'

var DMquerystring=null
var tipo_id='<?=$id?>'
var tipos=new Array(<?for($i=0;$i<$tipos->i;$i++){?>'<?=$tipos->id_tipo[$i]?>'<?if($i+1<$tipos->i){?>,<?}?><?}?>)

var path='<?=$c_path?>'
var lang='<?=$lang->lang?>'
var lang_path='<?=$lang->path_2?>'

var s_interadmin_preview='<?=$s_interadmin_preview?>'

var fullpath='http://'+location.host+'/'+path+lang_path

<?if($s_interadmin_preview&&!$isPopup){?>
	if(!parent.frames.length)location='http://'+location.host+'/'+path+'visualizar.php?redirect='+location.toString()
<?}?>
</script>
<script type="text/javascript" src="<?=$c_path_js?>interdyn.js"></script>
<script type="text/javascript" src="<?=$c_path_js?>interdyn_checkflash.js"></script>
<script type="text/javascript" src="<?=$c_path_js?>interdyn_form.js"></script>
<?if($c_menu!="none"){?>
	<?if(strpos($c_menu,"../")!==false){?>
		<script type="text/javascript" src="<?=$c_menu?>"></script>
	<?}else{?>
		<script type="text/javascript" src="<?if($c_menu){?><?=$c_path_js?>interdyn_menu_<?=$c_menu?><?}else{?>../../js/interdyn_menu_<?=$c_site?><?}?>.js"></script>
		<script type="text/javascript" src="../../js/interdyn_menu_str.php?lang=<?=$lang->lang?><?if($interadmin_gerar_menu){?>&interadmin_gerar_menu=<?=$interadmin_gerar_menu?><?}?>"></script>
	<?}?>
<?}?>
<script type="text/javascript" src="<?=$c_path_js?>interdyn_menu.js"></script>
<script type="text/javascript" src="../../js/functions.js"></script>
<script type="text/javascript" src="../../js/init.js"></script>
