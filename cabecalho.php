<?if($dhtml){?><div id="contentDiv" style="position:absolute;left:0;height:100%;margin-left:0px;margin-right:0px"><?}?>
<table width=100% height=100% border=0 cellspacing=0 cellpadding=0>
	<?if(!$dhtml||$site=="extranet"){?>
		<tr>
			<td height=1% colspan=5 valign="top">
				<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td height=60 align="center" bgcolor="#333333"><a href="../../index.php"><img src="../../img/jp7_home.gif" width=325 height=28 alt="JP7" border=0></a></td>
					</tr>
					<tr>
						<td height=20 align="center" bgcolor="#111111" class="font_cinza-4">
							<?if($site!="extranet"){?>
								<span style="letter-spacing:5px;word-spacing:15px">
								<?=makeMenuItem('servicos','serviços')?>
								<!-- <?=makeMenuItem('produtos','produtos')?> -->
								<?=makeMenuItem('cases','cases')?>
								<?=makeMenuItem('clientes','clientes')?>
								<?=makeMenuItem('contato','contato')?></span>
							<?}?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	<?}?>
	<tr>
		<td width=49% height=98%></td>
		<td style="background:#000"><img src="../../img/px.gif" width=1 height=1></td>
		<td align="center" valign="top" background="../../img/bg_overlay.gif">
			<table width=760 border=0 cellspacing=0 cellpadding=20><?if($site!="extranet"){?><tr><td height=20></td></tr><?}?><tr><td>
