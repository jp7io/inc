<?php
// General Vars
$interadmin_id=$id;
$interadmin_id_tipo=$id_tipo;
$interadmin_parent_id=$parent_id;
$interadmin_publish=($parent_id)?"S":$publish;

// Dates
if(!$id)$interadmin_date_insert=date("Y-m-d H:i:s");
$interadmin_date_modify=date("Y-m-d H:i:s");
$interadmin_date_publish=date("Y-m-d H:i:s");

// Log
$interadmin_log=date("d/m/Y H:i")." - ".(($user_log) ? $user_log : $s_user['login'])." - ".(($id)?"modify":"insert")." - ".$REMOTE_ADDR.chr(13);
if($id){
	$sql = "SELECT log FROM ".$db_prefix.$referer_lang_prefix.(($tipo_tabela)?"_".$tipo_tabela:"")." WHERE id=".$id;
	$rs=$db->Execute($sql)or die(jp7_debug($db->ErrorMsg(),$sql));;
	while ($row = $rs->FetchNextObj()) {
		$interadmin_log.=$row->log;
	}
	$rs->Close();
}

// Table Fields
$table_fields_notallowed=array("id","id_tipo","parent_id","date_insert","date_modify","date_key","date_1","date_publish","log","publish");
$table_columns=$db->MetaColumnNames($db_prefix.$referer_lang_prefix.(($tipo_tabela)?"_".$tipo_tabela:""));
array_shift($table_columns);
foreach ($table_columns as $table_field_name){
	$table_fields_arr[]=$table_field_name;
}

// Check Table Fields for Custom Tables
if($tipo_tabela){
	foreach($table_fields_arr as $field){
		if(strpos($field,"varchar_")===0||strpos($field,"select_")===0){
			$tipo_tabela_key=$field;
		}
	}
}

// Loop
for($i = 0;$i<$quantidade;$i++){
	if($varchar_key[$i]||$select_key[$i]||$quantidade<2||($tipo_tabela&&$GLOBALS[$tipo_tabela_key][$i])){
		// Campos
		foreach ($table_columns as $table_field_name){
			if (strpos($table_field_name, 'file_') === 0) {
				if ($_FILES[$table_field_name]['tmp_name'][$i]) {
					// Insert/Update
					$tipo = pathinfo($_FILES[$table_field_name]['name'][$i]);
					$keywords = basename($_FILES[$table_field_name]['name'][$i],'.' . $tipo['extension']);
					$tipo = strtolower($tipo['extension']);
					$invalid_extensions = array('php', 'php3', 'php4', 'php5', 'php6', 'phtml', 'inc', 'js');
					if (!in_array($tipo, $invalid_extensions)) {
						$lang_temp = $lang;
						$lang = $lang->lang;
						$id_arquivo_banco = jp7_db_insert($db_prefix . '_arquivos_banco', 'id_arquivo_banco', $id_arquivo_banco);
						$lang = $lang_temp;
						$id_arquivo_banco = str_pad($id_arquivo_banco, 8, '0', STR_PAD_LEFT);
						$path = '../../upload/' . (($id_tipo) ? toId(interadmin_tipos_nome($id_tipo, TRUE)) . '/' : '');
						$tipo = pathinfo($_FILES[$table_field_name]['name'][$i]);
						$tipo = strtolower($tipo['extension']);
						if (!is_dir($path)) mkdir($path, 0777);
						else @chmod($path, 0777);
						$dst = $path . $id_arquivo_banco . '.' . $tipo;
						copy($_FILES[$table_field_name]['tmp_name'][$i], $dst) or die("Erro na cópia do arquivo!");
						$GLOBALS['interadmin_' . $table_field_name] = $dst;
					}
				}
			}elseif(!array_search($table_field_name,$table_fields_notallowed)&&strpos($table_field_name,"date_")===false&&strpos($table_field_name,"time_")===false){
				/* Old Way
				if(strpos($table_field_name,"select_multi_")===0){
					eval("\$interadmin_field_value=\$".$table_field_name."[".$i."];");
				*/
				eval("\$interadmin_field_value=\$".$table_field_name."[".$i."];");
				if(is_array($interadmin_field_value)){
					if($interadmin_field_value)eval("\$interadmin_".$table_field_name."=implode(\",\",\$interadmin_field_value);");
					else eval("\$interadmin_".$table_field_name."=\"\";");
				}else{
					eval("\$interadmin_".$table_field_name."=\$".$table_field_name."[".$i."];");
					eval("\$interadmin_".$table_field_name."_xtra=\$".$table_field_name."_xtra[".$i."];");
				}
			}elseif(strpos($table_field_name,"time_")!==false){
				if($GLOBALS[$table_field_name."_i"][$i])$GLOBALS["interadmin_".$table_field_name]=$GLOBALS[$table_field_name."_H"][$i].":".$GLOBALS[$table_field_name."_i"][$i].":00";
			}elseif(strpos($table_field_name,"date_")!==false&&$table_field_name!="date_insert"&&$table_field_name!="date_modify"&&$table_field_name!="date_publish"){
				
				if ($GLOBALS[$table_field_name."_Y"][$i]) {
					$data = $GLOBALS[$table_field_name."_Y"][$i]."-".$GLOBALS[$table_field_name."_m"][$i]."-".$GLOBALS[$table_field_name."_d"][$i];
					$hora = " ".(($GLOBALS[$table_field_name."_H"][$i])?$GLOBALS[$table_field_name."_H"][$i]:"00").":".(($GLOBALS[$table_field_name."_i"][$i])?$GLOBALS[$table_field_name."_i"][$i]:"00").":00";
				
					//se a data for inválida, apaga os dados para nao dar erros de sqls
					//tenta usar a data completa			
					if (strtotime($data . $hora)) {
						$dataCorreta = $data . $hora;
					//se nao der, tenta usar ela sem hora
					} elseif (strtotime($data)) {
						$dataCorreta = $data." 00:00:00";
					//se nao der nao salvar nada, pois é inválida
					} else {
						$dataCorreta = "";
					}
					
					$GLOBALS["interadmin_".$table_field_name]=$dataCorreta;
				}
			}
			
		}
		
		// Password
		if($interadmin_password_key&&$interadmin_password_key_xtra)$interadmin_password_key=md5(strtolower($interadmin_password_key));
		// Text/HTML
		if(strtolower($interadmin_text_1)=="<p>&nbsp;</p>")$interadmin_text_1="";
		if($interadmin_text_1){
			$interadmin_text_1=toXHTML($interadmin_text_1);
			$pos1=strrpos($interadmin_text_1,".")+1;
			$interadmin_text_1_start=substr($interadmin_text_1,0,$pos1);
			$interadmin_text_1_end=substr($interadmin_text_1,$pos1);
			//$interadmin_text_1_end=str_replace("<br/>","",$interadmin_text_1_end);
			$interadmin_text_1_end=str_replace("<p>&nbsp;</p>","",$interadmin_text_1_end);
			$interadmin_text_1=$interadmin_text_1_start.$interadmin_text_1_end;
			$interadmin_text_1=str_replace(" </p>","</p>",$interadmin_text_1);
			//$interadmin_text_1=$db->qstr($interadmin_text_1,get_magic_quotes_gpc());
		}
		if(strtolower($interadmin_text_2)=="<p>&nbsp;</p>")$interadmin_text_2="";
		if($interadmin_text_2){
			$interadmin_text_2=toXHTML($interadmin_text_2);
			$pos1=strrpos($interadmin_text_2,".")+1;
			$interadmin_text_2_start=substr($interadmin_text_2,0,$pos1);
			$interadmin_text_2_end=substr($interadmin_text_2,$pos1);
			//$interadmin_text_2_end=str_replace("<br/>","",$interadmin_text_2_end);
			$interadmin_text_2_end=str_replace("<p>&nbsp;</p>","",$interadmin_text_2_end);
			$interadmin_text_2=$interadmin_text_2_start.$interadmin_text_2_end;
			$interadmin_text_2=str_replace(" </p>","</p>",$interadmin_text_2);
		}
		// Select_Multi
		if(!isset($interadmin_select_multi_1)&&isset($interadmin_select_multi_1_xtra))$interadmin_select_multi_1="";
		if(!isset($interadmin_select_multi_2)&&isset($interadmin_select_multi_2_xtra))$interadmin_select_multi_2="";
		
		// Insert/Update
		if($parent_id)$publish="";
		/*
		if($_SESSION['s_restrito_user_id']){
			$interadmin_publish="S";
		}else{
			$interadmin_publish="";
		}
		*/
		$interadmin_publish="S";
		$interadmin_char_key="S";		
		$interadmin_id=jp7_db_insert($db_prefix.$referer_lang_prefix.(($tipo_tabela)?"_".$tipo_tabela:""),"id",$interadmin_id,"interadmin_");
		
		if($quantidade>1)$interadmin_id="";
		// Disparo
		if($tipo_disparo)$tipo_disparo();
	}
}

// InterAdmin Log File
$file = fopen($c_doc_root . $config->name_id . '/interadmin/interadmin.log', 'w');
fwrite($file, $s_user['login']);
fclose($file);
//copy("interadmin.log",$c_cliente_physical_path."interadmin.log");
