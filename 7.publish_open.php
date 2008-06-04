<?
// Publish Check
if ($publish && !$s_interadmin_preview && !$interadmin_gerar_menu) {
	$publish_file_js = strpos($_SERVER['PHP_SELF'], 'js') !== false;
	$publish_file_xml = strpos($_SERVER['PHP_SELF'], 'xml') !== false;
	if ($publish_file_js) $publish_file_type = 'js';
	elseif ($publish_file_xml) $publish_file_type = 'xml';
	else $publish_file_type = 'htm';
	//echo $go_url;
	if ($publish_file_js || $publish_file_xml) {
		$publish_file = $lang->path . basename($_SERVER['PHP_SELF'], '.php') . '_P.' . $publish_file_type;
	} else {
		$publish_file = $_SERVER['REQUEST_URI'];
		$pos1 = strpos($publish_file, '?');
		if ($pos1 !== false) {
			$publish_file = substr($publish_file, 0, $pos1);
		}
		$pos1 = strrpos($publish_file, '/');
		if ($pos1 == strlen($publish_file) - 1) {
			$publish_file .= 'index.php';
		}
		$publish_file = str_replace('.php', '.htm', $publish_file);
		$publish_lang_path = str_replace('/', '', $lang->path_2);
		$publish_file = $_SERVER['DOCUMENT_ROOT'].str_replace($publish_lang_path, $publish_lang_path . '_P', $publish_file);
	}
	if (strpos($publish, 'multiple') === 0) {
		if ($publish_id) {
			$publish_multiple = $publish_id;
		} else {
			$publish_multiple = $_SERVER['QUERY_STRING'];
			$pos1 = strrpos($publish_multiple, '=') + 1;
			$publish_multiple = substr($publish_multiple, $pos1);
			$publish_multiple = str_replace('/', '_', $publish_multiple);
		}
		$publish_multiple_dir = $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['REQUEST_URI']);
		$publish_lang_path = str_replace('/', '', $lang->path_2);
		$publish_multiple_dir = str_replace($publish_lang_path, $publish_lang_path . '_P', $publish_multiple_dir);
		$publish_file = $publish_multiple_dir . '/' . basename($_SERVER['PHP_SELF'], '.php') . (($publish == 'multiple_dir')?'/':'_') . $publish_multiple . '.htm';
	}
	$filemtime = @filemtime($publish_file);
	if (!$publish_force && @filemtime($c_root.'interadmin.log') < $filemtime && date('d', $filemtime) == date('d')) {
		if ($publish_file_xml) {
			ob_start();
			readfile($publish_file);
			ob_end_flush();
			exit();
		} else {
			if (strpos($publish, 'multiple') === 0 && @include $publish_multiple) exit();
			elseif (@include $publish_file) exit();
		}
	}
}

// Publish Start
if ($publish && !$s_interadmin_preview) {
	$publish_open = true;
	header('pragma: no-cache');
	ob_start();
}
?>
