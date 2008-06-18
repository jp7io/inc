<?
// Publish Check
if ($publish && !$s_interadmin_preview && !$interadmin_gerar_menu) {
	//echo $go_url;
	$publish_file = $_SERVER['REQUEST_URI'];
	$pos1 = strpos($publish_file, '?');
	if ($pos1 !== false) {
		$publish_file = substr($publish_file, 0, $pos1);
	}
	$pos1 = strrpos($publish_file, '/');
	if ($pos1 == strlen($publish_file) - 1) {
		$publish_file .= 'index.php';
	}
	$publish_lang_path = str_replace('/', '', $lang->path_2);
	$publish_file = $c_doc_root . str_replace($publish_lang_path, $publish_lang_path . '_P', $publish_file);
	
	if (strpos($publish, 'multiple') === 0) {
		if ($publish_id) {
			$publish_multiple = $publish_id;
		} else {
			$publish_multiple = $_SERVER['QUERY_STRING'];
		}
		$publish_multiple_dir = $c_doc_root . dirname($_SERVER['REQUEST_URI']);
		$publish_lang_path = str_replace('/', '', $lang->path_2);
		$publish_multiple_dir = str_replace($publish_lang_path, $publish_lang_path . '_P', $publish_multiple_dir);
		$publish_file = $publish_multiple_dir . '/' . basename($_SERVER['PHP_SELF'], '.php') . '/' . $publish_multiple;
	}
	$filemtime = @filemtime($publish_file);
	if (!$_GET['publish_force'] && @filemtime($c_root.'interadmin.log') < $filemtime && date('d', $filemtime) == date('d')) {
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

if ($seo_publish && !$s_interadmin_preview && !$interadmin_gerar_menu) {

	$go_url = $_SERVER['REQUEST_URI'];
	
	$pos1 = strpos($go_url, '?');
	if ($pos1 !==false) $go_url = substr($go_url, 0, $pos1);
	
	$publish_file = $go_url;
	$publish_file = str_replace('.', '/', $publish_file);
	$publish_file = str_replace($c_path, '', $publish_file);
	$publish_file = $c_root . 'site_P' . $publish_file;
	
	$filemtime = @filemtime($publish_file);
	if (!$publish_force && @filemtime($c_root.'interadmin.log') < $filemtime && date('d', $filemtime) == date('d')) {
		@include $publish_file;
		exit();
	} else {
		$seo_publish_open = true;
		header('pragma: no-cache');
		ob_start();
	}

} else {

	// Publish Start
	if ($publish && !$s_interadmin_preview) {
		$publish_open = true;
		header('pragma: no-cache');
		ob_start();
	}

}
?>