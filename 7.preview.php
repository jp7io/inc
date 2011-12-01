<?php if ($s_session['preview'] || $config->server->type == InterSite::QA) { // Precisa ser Global ?>
	<?php
	if ($config->server->type == InterSite::QA) {
		$S = "QA";
	}
	if ($s_session['preview']) {
		if ($config->server->type == InterSite::QA) {
			$S .= " - " . strtoupper("Preview");
		} else {
			$S = strtoupper("Preview");
		}
	}
	?>
	<script>
	function interadmin_preview(){
		if(confirm("Deseja sair do modo PREVIEW e ir para o modo PUBLICADO?")) location='http://<?php echo $_SERVER['HTTP_HOST'] ?>/<?php echo $c_path ?>visualizar.php?redirect='+location.toString()
	}
	</script>
	<div class="preview_type" style="left:0px;border-width:0px 1px 1px 0px;background:#FFCC00;filter:alpha(opacity=50);z-index:1000"><?php echo $S ?></div>
	<div class="preview_type" style="right:0px;border-width:0px 0px 1px 1px;background:#FFCC00;filter:alpha(opacity=50);z-index:1000"><?php echo $S ?></div>
	<div class="preview_type" style="left:0px;border-width:0px 1px 1px 0px;z-index:1001"<?php /* onclick="interadmin_preview()"*/ ?>><?php echo $S ?></div>
	<div class="preview_type" style="right:0px;border-width:0px 0px 1px 1px;z-index:1001"<?php /* onclick="interadmin_preview()"*/ ?>><?php echo $S ?></div>
<?php } ?>
