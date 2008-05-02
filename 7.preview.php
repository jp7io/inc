<?if($s_interadmin_preview||$c_server_type=="QA"){ // Precisa ser Global?>
	<?
	if($c_server_type=="QA")$S="QA";
	if($s_interadmin_preview){
		if($c_server_type=="QA")$S.=" - ".strtoupper("Preview");
		else $S=strtoupper("Preview");
	}
	?>
	<script>
	function interadmin_preview(){
		if(confirm("Deseja sair do modo PREVIEW e ir para o modo PUBLICADO?"))location='http://<?=$HTTP_HOST?>/<?=$c_path?>visualizar.php?redirect='+location.toString()
	}
	</script>
	<div class="preview_type" style="left:0px;border-width:0px 1px 1px 0px;background:#FFCC00;filter:alpha(opacity=50);z-index:1000"><?=$S?></div>
	<div class="preview_type" style="right:0px;border-width:0px 0px 1px 1px;background:#FFCC00;filter:alpha(opacity=50);z-index:1000"><?=$S?></div>
	<div class="preview_type" style="left:0px;border-width:0px 1px 1px 0px;z-index:1001"<?/* onclick="interadmin_preview()"*/?>><?=$S?></div>
	<div class="preview_type" style="right:0px;border-width:0px 0px 1px 1px;z-index:1001"<?/* onclick="interadmin_preview()"*/?>><?=$S?></div>
<?}?>
