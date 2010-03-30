<?
// JP7's PHP DynForm Functions
// Copyright 2004-2007 JP7
// http://jp7.com.br
// Versão 0.30 - 2007/08/06

function interadmin_returnCampo($campo){
	global $xtra_disabledfields_arr_final;
	global $is;
	global $id;
	global $db, $db_prefix;
	global $s_user, $s_session;
	global $iframes_i, $quantidade, $j, $registros;
	global $select_campos_sql_temp;
	global $tit_start;
	global $lang, $l_selecione, $c_lang_varchar;
	if(is_array($campo)){
		$campo_array=$campo;
		$campo_nome=$campo["nome"];
		$ajuda=stripslashes($campo["ajuda"]);
		$tamanho=$campo["tamanho"];
		$obrigatorio=($quantidade>1)?"":$campo["obrigatorio"];
		$separador=$campo["separador"];
		$xtra=$campo["xtra"];
		$valor_default=$campo["default"];
		$readonly=$campo["readonly"];
		$campo=$campo["tipo"];
	}
	$valor=$GLOBALS[$campo];
	if(!$valor&&!$id)$valor=$valor_default;
	$_th="<th".(($obrigatorio||$readonly)?" class=\"".(($obrigatorio)?"obrigatorio":"").(($readonly)?" disabled":"")."\"":"").">".$campo_nome.":</th>\r\n";
	if($ajuda)$S_ajuda="<input type=\"button\" value=\"?\" tabindex=\"-1\" class=\"bt_ajuda\" onclick=\"alert('".$ajuda."')\" />";
	if($readonly=="hidden")$readonly_hidden=true;
	if($readonly||($campo_array[permissoes]&&$campo_array[permissoes]!=$s_user['tipo']&&!$s_user['sa']))$readonly=" disabled";
	
	if(strpos($campo,"tit_")===0){
		if($tit_start){
			echo "</tbody>";
			$tit_start=false;
		}
		echo "<tr><th colspan=4 class=\"inserir_tit_".(($xtra=="hidden")?"closed":"opened")."\" onclick=\"//interadmin_showTitContent(this)\">".$campo_nome."</th></tr><tbody".(($xtra=="hidden")?" style=\"display:none\"":"").">";
		$tit_start=true;
	}elseif(strpos($campo,"text_")===0){
		$form="<textarea".(($xtra)?" textarea_trigger=\"true\"":"")." name=\"".$campo."[]\" id=\"".$campo."_".$j."\" rows=".($tamanho+(($xtra)?((($xtra=="html_light"&&$tamanho<=5)||$quantidade>1)?2:5):0)).(($xtra)?" wrap=\"off\"":"")." xtra=\"".$xtra."\" class=\"inputs_width\" style=\"".(($xtra)?";color:#000066;font-family:courier new;font-size:11px;visibility:hidden":"")."\"".(((($campo=="text_0"||$campo=="text_1")&&$tamanho<=5)||$quantidade>1)?" smallToolbar=\"true\"":"").">".$valor."</textarea>";
		if($xtra)$form.="<script>interadmin_iframes[".$iframes_i."]='".$campo."_".$iframes_i++."'</script>";
	}elseif(strpos($campo,"char_")===0){
		if($xtra&&!$id)$GLOBALS[$campo]="S";
		$form=jp7_db_checkbox($campo."[".$j."]","S",$campo,$readonly);
	}elseif(strpos($campo,"select_multi_")===0){
		if(!$readonly_hidden){
			$form="<div class=\"select_multi\">";
			ob_start();
			if($xtra=="X"){
				include "select_multi.php";
				$campo_nome=interadmin_tipos_nome((is_numeric($campo_nome))?$campo_nome:0);
			}elseif($xtra){
				interadmin_tipos_combo(explode(",",$valor),(is_numeric($campo_nome))?$campo_nome:0,0,"","","checkbox",$campo."[".$j."][]",false,$readonly);
				$campo_nome="Tipos";
			}else{
				$temp_campo_nome=interadmin_tipos_nome((is_numeric((string) $campo_nome))?$campo_nome:0);
				echo interadmin_combo(explode(",",$valor),(is_numeric((string) $campo_nome))?$campo_nome:0,0,"","","checkbox",$campo."[".$j."][]",$temp_campo_nome,$obrigatorio);
				$campo_nome=$temp_campo_nome;
			}
			$form.=ob_get_contents();
			ob_end_clean();
			$form.="</div>";
			if($campo_array[label])$campo_nome=$campo_array[label];
		}
	}elseif(strpos($campo,"select_")===0){
		$campo_nome = (string) $campo_nome;
		if($campo_array[label])$campo_nome_2=$campo_array[label];
		else $campo_nome_2=($campo_nome=="all"&&$xtra)?"Tipos":interadmin_tipos_nome($campo_nome);
		$form="".
		"<select name=\"".$campo."[]\" label=\"".$campo_nome_2."\"".(($obrigatorio)?" obligatory=\"yes\"":"").$readonly." class=\"inputs_width\">".
		"<option value=\"\">" . $l_selecione . "</option>".
		"<option value=\"\">--------------------</option>";
		if ($xtra == 'radio') {
				$nomevarchar = 'varchar_' . $c_lang_varchar;
				$sql = "SELECT id," . $nomevarchar  . " FROM ".$db_prefix.
				" WHERE id_tipo=".$campo_nome.
				" ORDER BY int_key, char_key,varchar_key";
				$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));;
				$form = '';
				while ($row = $rs->FetchNextObj()) {
					$form.="<input ".(($obrigatorio)?" obligatory=\"yes\"":""). " label=\"".$campo_nome_2."\" type=\"radio\" name=\"" . $campo . "[".$j."]\" id=\"" . $campo . "_" . $j . "_".$row->id."\" value=\"".$row->id."\"".(($row->id==$valor)?" checked":"")." /> <label for=\"" . $campo . "_" . $j . "_".$row->id."\">".toHTML($row->$nomevarchar)."</label>";
				}
				$rs->Close();
		} elseif ($xtra) {
			if($campo_nome=="all"){
				ob_start();
				interadmin_tipos_combo($valor,0);
				$form.=ob_get_contents();
				ob_end_clean();
			}else{
				$sql = "SELECT id_tipo,nome FROM ".$db_prefix."_tipos".
				" WHERE parent_id_tipo=".$campo_nome.
				" ORDER BY ordem,nome";
				$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
				while ($row = $rs->FetchNextObj()) {
					$form.="<option value=\"".$row->id_tipo."\"".(($row->id_tipo==$valor)?" SELECTED":"").">".toHTML($row->nome)."</option>";
				}
				$rs->Close();
			}
		}else{		
			$form="<select name=\"".$campo."[]\" label=\"".$campo_nome_2."\" xtype=\"autocomplete\"".(($obrigatorio)?" obligatory=\"yes\"":"").$readonly." class=\"inputs_width\">".
			"<option value=\"\">" . $l_selecione . (($select_campos_2_nomes)?$select_campos_2_nomes:"")."</option>".
			"<option value=\"\">--------------------</option>".
			interadmin_combo($valor,(is_numeric($campo_nome))?$campo_nome:0,0,"","","combo",$campo."[".$j."]",$temp_campo_nome,$obrigatorio);
		}
		if ($xtra != 'radio') $form.="</select>";
		$campo_nome=$campo_nome_2;
	}elseif(strpos($campo,"int_")===0||strpos($campo,"float_")===0){
		$onkeypress=" onkeypress=\"return DFonlyThisChars(true,false,' -.,()')\"";
		if($campo=="int_key"&&!$valor&&$quantidade>1)$valor=$registros+1+$j;
		$form="<input type=\"text\" name=\"".$campo."[]\" label=\"".$campo_nome."\" value=\"".$valor."\" maxlength=255".(($obrigatorio)?" obligatory=\"yes\"":"")." style=\"width:".(($tamanho)?$tamanho."em":"70px")."\"".$readonly.$onkeypress.">";
	}else{
		$onkeypress="";
		if(strpos($campo,"varchar_")===0){
			switch($xtra){
				case "id": // ID
					$onkeypress=" onkeypress=\"return DFonlyThisChars(true,true,'_')\" onblur=\"ajax_function(this,'interadmin_inserir_checkuniqueid.php?id_tipo=".$GLOBALS["id_tipo"]."&campo=".$campo."&valor_atual=".$valor."&valor='+value,interadmin_inserir_checkUniqueId)\"";
					break;
				case "email": // E-Mail
					$onkeypress=" xtype=\"email\" onkeypress=\"return DFonlyThisChars(true,true,'_@-.')\"";
					break;
				case "id_email": // ID E-Mail
					$onkeypress=" xtype=\"email\" onkeypress=\"return DFonlyThisChars(true,true,'_@-.')\"";
					break;
				case "num": // Número
					$onkeypress=" onkeypress=\"return DFonlyThisChars(true,false,' -.,()')\"";
					break;
				case "cpf": // CPF
					$onkeypress=" xtype=\"cpf\"";
					break;
			}
		}
		$form="<input type=\"".((strpos($campo,"password_")===0)?"password":"text")."\"".((strpos($campo,"password_")===0)?" xtype=\"password\"":"")." name=\"".$campo."[]\" label=\"".$campo_nome."\" value=\"".toForm($valor)."\" title=\"".$ajuda."\" maxlength=255".(($obrigatorio)?" obligatory=\"yes\"":"").$readonly." class=\"inputs_width\"".(($tamanho)?" style=\"width:".$tamanho."em\"":"").$onkeypress." xtra=\"".$xtra."\" />";
	}
	$form.="<input type=\"hidden\" name=\"".$campo."_xtra[]\" value=\"".$xtra."\"".$readonly." />";
	if($readonly&&$valor_default)$form.="<input type=\"hidden\" name=\"".$campo."[]\" value=\"".$valor."\" />";
	
	if($campo_nome){
		if(strpos($campo,"tit_")===0){
			
		}elseif(strpos($campo,"file_")===0){
			if(strpos($valor,"../../")===0)$url=substr($valor,6);
			echo "".
			"<tr>\r\n".
				$_th.
				"<td colspan=\"3\"><input type=\"file\" name=\"".$campo."[".$j."]\" value=\"".$valor."\" maxlength=\"255\"".$readonly." class=\"inputs_width\" /></td>\r\n".
			"</tr>\r\n";
		}elseif(strpos($campo,"date_")===0){
			$S="".
			"<tr>\r\n".
				$_th.
				"<td colspan=\"2\">\r\n".
					((strpos($xtra,"calendar_")!==false)?"<input type=\"hidden\" id=\"".$campo."_calendar_value_".$j."\" value=\"".$valor."\" />":"").
					"<table width=\"100%\">\r\n".
						"<tr>\r\n".
							"<td>".jp7_app_createSelect_date($campo,(($xtra=="S"||(strpos($xtra,"datetime")===false&&$xtra))?"style=\"visibility:hidden\"":"").(($xtra=="calendar_datetime"||$xtra=="calendar_date")?" onchange=\"interadmin_calendar_update_bycombo(this,'".$campo."','".$j."')\"":"").$readonly,false,$j,$readonly.(($xtra=="calendar_datetime"||$xtra=="calendar_date")?" onchange=\"interadmin_calendar_update_bycombo(this,'".$campo."','".$j."')\"":""),$xtra,$obrigatorio)."</td>\r\n".
							"<td width=\"99%\" align=\"right\">\r\n".
								//"<input type=\"button\" value=\"Atualizar".((strpos($xtra,"calendar")===false)?" Data".(($xtra!="S")?" - Hora":""):"")."\"".$readonly." tabindex=\"-1\" onclick=\"refreshDate('".$campo."','".$j."','','".$xtra."')".(($xtra=="calendar_datetime"||$xtra=="calendar_date")?";interadmin_calendar_update_bycombo(this,'".$campo."','".$j."')\"":"")."\">\r\n".
								((strpos($xtra,"calendar")!==false)?"<input type=\"button\" id=\"".$campo."_calendar_".$j."\" value=\"Calendário\"".$readonly." tabindex=\"-1\" style=\"margin-left:10px\" />":"").
							"</td>\r\n".
						"</tr>\r\n".
					"</table>\r\n".
				"</td>\r\n".
				"<td>".$S_ajuda."</td>\r\n".
			"</tr>\r\n";
			echo $S;
		}elseif(strpos($campo,"password_")===0&&$valor&&$xtra){
			echo "".
			"<tr>".
				$_th.
				"<td colspan=\"2\">\r\n".
					"<table width=\"100%\">\r\n".
						"<tr>\r\n".
							"<td width=\"99%\" style=\"display:none\"><input type=\"password\" name=\"".$campo."[".$j."]\" label=\"".$campo_nome."\" disabled style=\"width:100%\"><input type=\"hidden\" name=\"".$campo."_xtra[".$j."]\" value=\"".$xtra."\"></td>\r\n".
							"<td><input type=\"button\" value=\"Alterar...\" onclick=\"interadmin_inserir_password(this,'".$campo."[".$j."]')\"><input type=\"text\" disabled style=\"width:1px;visibility:hidden\"></td>\r\n".
						"</tr>\r\n".
					"</table>\r\n".
				"</td>\r\n".
				"<td>".$S_ajuda."</td>\r\n".
			"</tr>\r\n";
		}elseif(strpos($campo,"special_")===0){
			echo $campo_nome($campo_array,$valor);
		}else{
			if(!$readonly_hidden){
				echo "".
				"<tr".(($s_session['mode']=="light"&&strpos($campo,"text_")===0&&$xtra)?" style=\"display:none\"":"").">\r\n".
					"<th".(($obrigatorio||$readonly)?" class=\"".(($obrigatorio)?"obrigatorio":"").(($readonly)?" disabled":"")."\"":"").">".$campo_nome.":</th>\r\n".
					"<td colspan=2>".$form."</td>\r\n".
					"<td>".$S_ajuda."</td>\r\n".
				"</tr>\r\n";
				if(strpos($campo,"password_")===0){
					echo "".
					"<tr".(($s_session['mode']=="light"&&strpos($campo,"text_")===0&&$xtra)?" style=\"display:none\"":"").">\r\n".
						"<th".(($obrigatorio||$readonly)?" class=\"".(($obrigatorio)?"obrigatorio":"").(($readonly)?" disabled":"")."\"":"").">Confirm. de ".$campo_nome.":</th>\r\n".
						"<td colspan=2><input type=\"password\" xtype=\"password\" id=\"".$campo."_confirm[]\" name=\"".$campo."[]\" label=\"Confirmação de ".$campo_nome."\" value=\"".toForm($valor)."\" title=\"".$ajuda."\" maxlength=255".(($obrigatorio)?" obligatory=\"yes\"":"").$readonly." class=\"inputs_width\"".(($tamanho)?" style=\"width:".$tamanho."em\"":"").$onkeypress."></td>\r\n".
						"<td>".$S_ajuda."</td>\r\n".
					"</tr>\r\n";
				}
			}else{
				echo $form;
			}
		}
	}
	if($separador){
		if($tit_start){
			echo "</tbody>\r\n";
			$tit_start=false;
		}
		echo "<tr><td height=".(($quantidade>1||$s_session['screenwidth']<=800)?5:10)." colspan=4></td></tr>\r\n";
	}
}

// 2007/01/04 by JP
function interadmin_combo($current_id,$parent_id_tipo_2,$nivel=0,$prefix="",$sql_where="",$style="select",$field_name="",$field_label="",$obrigatorio=""){
	global $id_tipo;
	global $db;
	global $db_prefix;
	global $select_campos_sql_temp;
	global $lang;
	$sql = "SELECT tabela,campos,language FROM ".$db_prefix."_tipos".
	" WHERE id_tipo=".$parent_id_tipo_2;
	
	$rs = $db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
	while($row=$rs->FetchNextObj()){
		$campos=interadmin_tipos_campos($row->campos);
		$select_lang=$row->language;
		$select_tabela=$row->tabela;
	}
	$rs->Close();
	// Combo Fields
	if($campos){
		foreach($campos as $select_campo){
			if($select_campo[combo]){
				if(strpos($select_campo[tipo],"special_")===0){
					$select_campos_2_nomes.=" - ".$select_campo[nome]("select_campos_sql_temp",$select_campos_sql_temp,"header");
				}else{
					$select_campos_2_nomes.=" - ".((intval($select_campo[nome])>0)?interadmin_tipos_nome(intval($select_campo[nome])):(($select_campo[nome]=="all")?"Tipos":$select_campo[nome]));
				}
				if($select_campo[tipo]!="varchar_key"){
					$select_campos_2.=",".$select_campo[tipo];
					$select_campos_2_array[]=$select_campo[tipo];
					$select_campos_2_xtra[]=$select_campo[xtra];
					$select_campos_2_nomes_arr[]=$select_campo[nome];
				}
			}
		}
	}
	// Loop
	if($select_tabela){
		$sql = "SELECT id,varchar_key,deleted".$select_campos_2." FROM ".$db_prefix."_".$select_tabela.
		" ORDER BY varchar_key";
	}else{
		$sql = "SELECT id,varchar_key,deleted".$select_campos_2." FROM ".$db_prefix.(($select_lang)?$lang->prefix:"").
		" WHERE id_tipo=".$parent_id_tipo_2.
		$sql_where.
		" ORDER BY int_key,varchar_key,select_key";
	}
	$rs=interadmin_query($sql);
	$S='';
	for($i = 0;$i<$nivel*5;$i++){
		if($i<$nivel*5-1)$S.='-';
		else $S.='> ';
	}
	$numRows = $rs->RecordCount();
	if($style=="checkbox")$R.="<input type=\"checkbox\" id=\"".$field_name."_all\" value=\"\"".((is_array($current_id)&&$numRows==count($current_id))?" checked style=\"color:blue\"":"").(($row->id==$id)?" style=\"color:red\"":"").((interadmin_tipos_nome($parent_id_tipo_2)=="Classes")?" style=\"background:#DDD\"":"")." onclick=\"DFselectAll(this)\"><label for=\"".$field_name."_all\" unselectable=\"on\"".(($selected)?" style=\"color:blue\"":"").">".strtoupper($GLOBALS['l_todos'])."</label><br>\n";
	elseif($style=="combo"){
		$R.="<option value=\"\" style=\"color:#ccc\">".$select_campos_2_nomes."</option>";
	}
	while($row=$rs->FetchNextObj()){
		if(is_array($current_id))$selected=in_array($row->id,$current_id);
		else $selected=($row->id==$current_id);
		if ($row->select_key&&!in_array("select_key",$select_campos_2_array)){
			if($campos[select_key][xtra])$row->varchar_key=interadmin_tipos_nome($row->select_key);
			else $row->varchar_key=jp7_fields_values($row->select_key);
		}
		// Combo Fields
		$select_campos_sql="";
		if($select_campos_2_array){
			foreach($select_campos_2_array as $key=>$value){
				$select_campos_sql_temp=$row->$value;
				if($select_campos_sql_temp){
					if(strpos($value,"special_")===0){
						$select_campos_sql.=(($key||$row->varchar_key)?" - ":"").$select_campos_2_nomes_arr[$key]("select_campos_sql_temp",$select_campos_sql_temp,"list");
					}else{
						if(is_numeric($select_campos_sql_temp)&&strpos($value,"varchar_")===false&&strpos($value,"int_")===false)$select_campos_sql_temp=($select_campos_2_xtra[$key])?interadmin_tipos_nome($select_campos_sql_temp):jp7_fields_values($select_campos_sql_temp);
						$select_campos_sql.=(($key||$row->varchar_key)?" - ":"").$select_campos_sql_temp;
					}
				}
			}
		}
		// Output
		if($style=="checkbox")$R.="<input type=\"checkbox\" name=\"".$field_name."\" id=\"".$field_name."_".$row->id."\" label=\"".$field_label."\" value=\"".$row->id."\"".(($obrigatorio)?" obligatory=\"yes\"":"").(($selected)?" checked style=\"color:blue\"":"").(($row->id==$id)?" style=\"color:red\"":"").((interadmin_tipos_nome($parent_id_tipo_2)=="Classes")?" style=\"background:#DDD\"":"")." onclick=\"DFselectAllCheck(this)\"><label for=\"".$field_name."_".$row->id."\" unselectable=\"on\"".(($selected)?" style=\"color:blue\"":"").">".$S.$row->varchar_key.jp7_string_left($select_campos_sql,100)."</label><br>\n";
		else $R.="<option value=\"".$row->id."\"".(($selected)?" SELECTED style=\"color:blue\"":"").(($row->id==$id)?" style=\"color:red\"":"").((interadmin_tipos_nome($parent_id_tipo_2)=="Classes")?" style=\"background:#DDD\"":"").">"./*substr($row->varchar_key,0,1).")".*/$S.$row->varchar_key.jp7_string_left($select_campos_sql,100)."</option>\n";
		//if($style!="checkbox"||$nivel<2)interadmin_tipos_combo($current_id_tipo,$row->id_tipo,$nivel+1,$prefix,"",$style,$field_name);
	}
	$rs->Close();
	return $R;
}

// 2006/10/19 by JP
function interadmin_tipos_combo($current_id_tipo,$parent_id_tipo_2,$nivel=0,$prefix="",$sql_where="",$style="select",$field_name="",$classes=false,$readonly=""){
	global $id_tipo;
	global $db;
	global $db_prefix;
	$sql = "SELECT id_tipo,nome FROM ".$db_prefix."_tipos".
	" WHERE parent_id_tipo=".$parent_id_tipo_2.
	$sql_where.
	" ORDER BY ordem,nome";
	$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
	$S='';
	for($i = 0;$i<$nivel*5;$i++){
		if($i<$nivel*5-1)$S.='-';
		else $S.='> ';
	}
	$i = 0;
	while ($row = $rs->FetchNextObj()) {
		if(is_array($current_id_tipo))$selected=in_array($row->id_tipo,$current_id_tipo);
		else $selected=($row->id_tipo==$current_id_tipo);
		if(interadmin_tipos_nome($parent_id_tipo_2)=="Classes"||$classes)$classes=true;
		if($style=="checkbox"){
			if(!$i&&!$nivel){
				if(is_array($current_id_tipo))$selected_2=in_array("N",$current_id_tipo);
				else $selected_2=("N"==$current_id_tipo);
				echo "<input type=\"checkbox\" name=\"".$field_name."\" id=\"".$field_name."_N\" value=\"N\"".$readonly.(($selected_2)?" checked style=\"color:blue\"":"").(($row->id_tipo=="N")?" style=\"color:red\"":"").(($classes)?" style=\"background:#DDD\"":"")."><label for=\"".$field_name."_N\" unselectable=\"on\"".(($selected_2)?" style=\"color:blue\"":"").">NENHUM</label><br>\n";
			}
			echo "<input type=\"checkbox\" name=\"".$field_name."\" id=\"".$field_name."_".$row->id_tipo."\" value=\"".$row->id_tipo."\"".$readonly.(($selected)?" checked style=\"color:blue\"":"").(($row->id_tipo==$id_tipo)?" style=\"color:red\"":"").((interadmin_tipos_nome($parent_id_tipo_2)=="Classes")?" style=\"background:#DDD\"":"")."><label for=\"".$field_name."_".$row->id_tipo."\" unselectable=\"on\"".(($selected)?" style=\"color:blue\"":"").">".$S.$row->nome."</label><br>\n";
		}else echo "<option value=\"".$row->id_tipo."\"".$readonly.(($selected)?" SELECTED style=\"color:blue\"":"").(($row->id_tipo==$id_tipo)?" style=\"color:red\"":"").(($classes)?" style=\"background:#DDD\"":"").">".substr($row->nome,0,1).")".$S.$row->nome."</option>\n";
		if($style!="checkbox"||$nivel<2)interadmin_tipos_combo($current_id_tipo,$row->id_tipo,$nivel+1,$prefix,"",$style,$field_name,$classes,$readonly);
		$i++;
	}
	$rs->Close();
}

// jp7_DF_sendMail (2007/08/06 by JP)
function jp7_DF_sendMail($post_vars,$from_info=false,$env_info=true,$attachments="",$parameters=""){
	// Check Server
	$server="";
	global $OS;
	if(strpos($OS,"Windows")!==false)$server="Windows";
	// Variables
	foreach($post_vars as $key=>$value){
		if(strpos($key,"DF_")===0||$key=="debug")eval("\$".$key."=\"".$value."\";");
	}
	if($debug){
		$DF_to=($DF_from)?$DF_from:"debug@jp7.com.br";
		$DF_to_name.=" (Debug)";
	}
	if(!$DF_client)$DF_client=$DF_to;
	if(!$DF_client_name)$DF_client_name=$DF_to_name;
	if($DF_cc_name)$DF_cc=$DF_cc_name." <".$DF_cc.">";
	if($DF_bcc_name)$DF_bcc=$DF_bcc_name." <".$DF_bcc.">";
	if($DF_template=="default")$DF_template="http://".$_SERVER["HTTP_HOST"]."/".$GLOBALS["c_path"]."site/_templates/form_htm.php";
	$DF_template.=((strpos($DF_template,"?")===false)?"?":"&")."DF_client_name=".$post_vars["DF_client_name"]."&DF_template_title=".$post_vars["DF_template_title"];
	$html=($DF_format=="text")?false:true;
	$vars_required=array(
		"DF_to",
		"DF_subject"
	);
	$vars_headers=array(
		"DF_cc",
		"DF_bcc"
	);
	$vars_special=array(
		"DF_client",
		"DF_client_name",
		"DF_from",
		"DF_from_name",
		"DF_to",
		"DF_to_name",
		"DF_cc_name",
		"DF_bcc_name",
		"DF_subject",
		"DF_template",
		"DF_template_title",
		"DF_reply_subject",
		"DF_reply_message",
		"DF_reply_template",
		"DF_redirect",
		"DF_reset",
		"DF_submit",
		"DF_format",
		"DF_reply_none",
		"DF_teste",
		"DF_reply_to",
		"DF_reply_to_name",
		"debug"
	);
	// Check Data
	if(!$DF_to||!$DF_subject){
		echo 'Faltam parâmetros <b>$DF_to</b> ou <b>$DF_subject</b>. Linha:' . __LINE__ . ' Arquivo: ' . __FILE__;
		exit;
	}
	// Send Mail
	$headers="";
	if($DF_from){
		if($DF_from_name)$headers.="From: ".jp7_encode_mimeheader($DF_from_name)." <".$DF_from.">\r\n";
		else $headers.="From: ".$DF_from."\r\n";
	}
	if($DF_reply_to){
		$headers.="Reply-To: ".$DF_reply_to_name." <".$DF_reply_to.">\r\n";
	}
	$message="".
	"<b>".$DF_subject."</b><br>\r\n".
	"<hr size=1 color=\"#666666\"><br>\r\n";
	if($from_info){
		$message.="<font size=1><b>Nome:</b></font> ".$DF_from_name."<br>\r\n";
		$message.="<font size=1><b>E-Mail:</b></font> ".$DF_from."<br><br>\r\n";
	}
	foreach($post_vars as $key=>$value){
		if(in_array($key,$vars_headers)){
			if(!$debug/*&&!@ini_get("safe_mode")*/ && $value)$headers.=strtoupper(substr($key,3)).": ".$value."\r\n";
		}elseif(strpos($key,"DF_spacer")===0){
			$message.="<br>\r\n";
		}elseif(strpos($key,"_select_multi")!==false){
			$value=str_replace(" ,","<br>",$value); // PC to HTML
			$value=str_replace("<br>","<br>\r\n",$value); // HTML to HTML with CRLF
			$message.="<font size=1><b>".substr($key,0,strlen($key)-13).":</b></font>&nbsp;<br>\r\n".
			"<div style=\"background:#F2F2F2;margin-top:3px;padding:5px;border:1px solid #CCC\">\r\n".
			"<font face=\"verdana\" size=2 color=\"#000000\" style=\"font-size:13px\">\r\n".
			toHTML($value,true)."\r\n".
			"</font>\r\n".
			"</div>\r\n";
		}elseif(strpos($key,"_textarea")!==false){
			$value=str_replace("\r\n","<br>",$value); // PC to HTML
			$value=str_replace("\r","<br>",$value); // Mac to HTML
			$value=str_replace("\n","<br>",$value); // Linux to HTML
			$value=str_replace("<br>","<br>\r\n",$value); // HTML to HTML with CRLF
			$message.="<font size=1><b>".substr($key,0,strlen($key)-9).":</b></font>&nbsp;<br>\r\n".
			"<div style=\"background:#F2F2F2;margin-top:3px;padding:5px;border:1px solid #CCC\">\r\n".
			"<font face=\"verdana\" size=2 color=\"#000000\" style=\"font-size:13px\">\r\n".
			toHTML($value,true)."\r\n".
			"</font>\r\n".
			"</div>\r\n";
		}elseif(strpos($key,"_link")!==false){
			$message.="<font size=1><b>".substr($key,0,strlen($key)-5).":</b></font> <a href=\"".$value."\" target=\"_blank\">".$value."</a><br>\r\n";
		}elseif(strpos($key,"file_")!==false||@is_link($value)){
			$message.="<font size=1><b>".$key.":</b></font> <a href=\"".$value."\" target=\"_blank\">".$value."</a><br>\r\n";
		}elseif(!in_array($key,$vars_special)&&strpos($key,"noDF_")===false){
			$message.="<font size=1><b>".$key.":</b></font> ".$value."<br>\r\n";
		}
	}
	if($env_info){
		global $lang;
		global $REMOTE_ADDR;
		$message.="".
		"<br>\r\n".
		"<hr size=1 color=\"#666666\">\r\n".
		"<font size=1 color=\"#333333\">\r\n".
		(($lang)?"<b>Idioma:</b> ".$lang->lang."<br>\r\n":"").
		"<b>Data - Hora de Envio:</b> ".date("d/m/Y - H:i:s")."<br>\r\n".
		"<b>IP:</b> ".$REMOTE_ADDR."<br><br>\r\n".
		"</font>\r\n";
	}
	if($debug)$DF_send=jp7_mail(($DF_to_name&&$server!="Windows"&&!@ini_get("safe_mode"))?$DF_to_name." <".$DF_to.">":$DF_to,$DF_subject,$message,$headers,$parameters,$DF_template,$html,$attachments);
	else $DF_send=@jp7_mail(($DF_to_name&&$server!="Windows"&&!@ini_get("safe_mode"))?$DF_to_name." <".$DF_to.">":$DF_to,$DF_subject,$message,$headers,$parameters,$DF_template,$html,$attachments);
	// Send Reply
	if($DF_from&&!$DF_reply_none){
		$headers="";
		if($DF_client){
			if($DF_client_name)$headers.="From: ".$DF_client_name." <".$DF_client.">\r\n";
			else $headers.="From: ".$DF_client."\r\n";
		}
		if($DF_reply_message){
			$reply_subject=($DF_reply_subject)?$DF_reply_subject:"Resposta Automática";
			$message=$DF_reply_message;
			if($DF_from_name)$message=str_replace("%FROM_NAME%",$DF_from_name,$message);
			if($DF_client_name)$message=str_replace("%CLIENT_NAME%",$DF_client_name,$message);
		}else{
			if($lang->lang=="en"){
				$reply_subject="Automatic Message";
				$message="".
				(($DF_from_name)?"<b>".$DF_from_name.",</b><br><br>\r\n":"").
				"We received your message and we are grateful for your interest.<br>\r\n".
				"We will contact you soon.<br><br>\r\n".
				(($DF_client_name)?"Yours faithfully,<br><br>\r\n":"").
				(($DF_client_name)?"<b>".$DF_client_name.".</b><br><br>\r\n":"").
				"<hr size=1 color=\"#666666\">\r\n".
				"<font size=1 color=\"#333333\">Automatic message. Please do not respond.</font><br><br>\r\n";
			}else{
				$reply_subject="Resposta Automática";
				$message="".
				(($DF_from_name)?"<b>".$DF_from_name.",</b><br><br>\r\n":"").
				"Recebemos sua mensagem e agradecemos o interesse.<br>\r\n".
				"Em breve entraremos em contato.<br><br>\r\n".
				(($DF_client_name)?"Atenciosamente,<br><br>\r\n":"").
				(($DF_client_name)?"<b>".$DF_client_name.".</b><br><br>\r\n":"").
				"<hr size=1 color=\"#666666\">\r\n".
				"<font size=1 color=\"#333333\">Mensagem automática. Favor não responder.</font><br><br>\r\n";
			}
		}
		if($DF_reply_to){
			$reply_to=($DF_from_name&&$server!="Windows"&&!@ini_get("safe_mode"))?jp7_encode_mimeheader($DF_reply_to_name)." <".$DF_reply_to.">":$DF_from;
		}else{
			$reply_to=($DF_from_name&&$server!="Windows"&&!@ini_get("safe_mode"))?jp7_encode_mimeheader($DF_from_name)." <".$DF_from.">":$DF_from;
		}
		if($debug)$DF_reply_send=jp7_mail($reply_to,"Site ".$DF_client_name." - ".$reply_subject,($DF_reply_template&&$DF_from_name)?$DF_from_name:$message,$headers,"",($DF_reply_template)?$DF_reply_template:$DF_template,$html,$attachments);
		else $DF_reply_send=@jp7_mail($reply_to,"Site ".$DF_client_name." - ".$reply_subject,($DF_reply_template&&$DF_from_name)?$DF_from_name:$message,$headers,"",($DF_reply_template)?$DF_reply_template:$DF_template,$html,$attachments);
	}
	// Redirect
	//if(!$DF_redirect)$DF_redirect="sendmail_ok.php";
	if($DF_redirect){
		$DF_redirect_querystring="DF_send=".$DF_send."&DF_from_name=".urlencode($DF_from_name).((!$DF_send)?"&DF_error=".urlencode($GLOBALS[php_errormsg]):"");
		$DF_redirect.=((strpos($DF_redirect,"?")===false)?"?":"&").$DF_redirect_querystring;
		if($debug)echo "Location: ".$DF_redirect;
		else header("Location: ".$DF_redirect);
	}else return $DF_send;
}

// jp7_DF_prepareVars (2006/03/09)
function jp7_DF_prepareVars($db_prefix,$id_tipo,$vars_in,$var_prefix="",$only_interadmin=false){
	global $db;
	global $db_name;
	$vars_out=array();
	foreach($vars_in as $key=>$value){
		if(strpos($key,"noDF_")===false){
			// InterAdmin Vars
			if(strpos($key,$var_prefix."varchar_")!==false
				||strpos($key,$var_prefix."password_")!==false
				||strpos($key,$var_prefix."text_")!==false
				||strpos($key,$var_prefix."char_")!==false
				||strpos($key,$var_prefix."int_")!==false
				||strpos($key,$var_prefix."float_")!==false
				||strpos($key,$var_prefix."date_")!==false
				||strpos($key,$var_prefix."file_")!==false
				||strpos($key,$var_prefix."select_")!==false
				||strpos($key,$var_prefix."parent_id")!==false){
				if($var_prefix)$key=substr($key,strlen($var_prefix));
				$campo=interadmin_tipos_campo($db_prefix,$id_tipo,$key);
				$key_out=$campo[nome];
				if(!$key_out)$key_out=$key;
				if(strpos($key,"password_")===0){
					$new_value="";
					for($i = 0;$i<strlen($value);$i++){
						$new_value.="*";
					}
					$value=$new_value;
				}elseif(strpos($key,"char_")===0){
					if($value=="S")$value="Sim";
					elseif(!$value||$value=="N")$value="Não";
				}elseif(strpos($key,"date_")===0){
					$value=jp7_date_format($value,"d/m/Y H:i");
				}elseif(strpos($key,"select_multi")===0){
					// Selects Multi
					if($key_out&&is_int(intval($key_out))){
						$sql = "SELECT nome FROM ".$db_prefix."_tipos WHERE id_tipo=".intval($key_out);
						$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
						if ($row=$rs->FetchNextObj())$key_out=$row->nome."_select_multi";
						$rs->Close();
					}
					if($value/*&&is_int(intval($value))*/){
						if($campo[xtra]){
							$sql = "SELECT nome FROM ".$db_prefix."_tipos WHERE id_tipo IN (".$value.")";
							$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
							while ($row = $rs->FetchNextObj()) {
								$value_arr[]=$row->nome;
							}
							$rs->Close();
						}else{
							$sql = "SELECT varchar_key FROM ".$db_prefix." WHERE id IN (".$value.")";
							$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
							while ($row = $rs->FetchNextObj()) {
								$value_arr[]=$row->varchar_key;
							}
							$rs->Close();
						}
						$value=join(" ,",$value_arr);
					}
				}elseif(strpos($key,"select_")===0){
					// Selects
					if($key_out&&is_int(intval($key_out))){
						$sql = "SELECT nome FROM ".$db_prefix."_tipos WHERE id_tipo=".intval($key_out);
						$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
						if ($row=$rs->FetchNextObj())$key_out=$row->nome;
						$rs->Close();
					}
					if($value&&is_int(intval($value))){
						if($campo[xtra]){
							$sql = "SELECT nome FROM ".$db_prefix."_tipos WHERE id_tipo=".intval($value);
							$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
							if ($row=$rs->FetchNextObj())$value=$row->nome;
							$rs->Close();
						}else{
							$sql = "SELECT varchar_key FROM ".$db_prefix." WHERE id=".intval($value);
							$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
							if ($row=$rs->FetchNextObj())$value=$row->varchar_key;
							$rs->Close();
						}
					}
				}elseif(strpos($key,"parent_id")===0){
					// Parent ID
					if($value){
						$sql = "SELECT id_tipo,varchar_key FROM ".$db_prefix." WHERE id=".$value;
						$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
						$row=$rs->FetchNextObj();
						$key_out=$row->id_tipo;
						$value=$row->varchar_key;
						$rs->Close();
					}
					if($key_out){
						$sql = "SELECT nome FROM ".$db_prefix."_tipos WHERE id_tipo=".$key_out;
						$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));
						$row=$rs->FetchNextObj();
						$key_out=$row->nome;
						$rs->Close();
					}
				}
				if(strpos($key,"text_")===0){
					// Texts
					$key=$key_out."_textarea";
				}elseif(strpos($key,"file_")===0){
					// Texts
					$key=$key_out."_link";
				}else $key=$key_out;
				if(strpos($key,"password_key_check")===false)$vars_out[$key]=$value;
			}
			// All
			if(strpos($key,"DF_")===0||!$only_interadmin)$vars_out[$key]=$value;
		}
	}
	return $vars_out;
}
?>
