<?
if ($publish && !$s_interadmin_preview){
	$file_content = ob_get_contents();
	$file_content = str_replace(chr(9), '', $file_content);
	$file_content = str_replace(chr(13), '', $file_content);
	if (strlen($file_content) > 10) {
		if (strpos($publish, 'multiple') === 0) {
			if (!is_dir($publish_multiple_dir)) mkdir($publish_multiple_dir);
			if (!is_dir($publish_multiple_dir . '/' . basename($_SERVER['PHP_SELF'], '.php'))) mkdir($publish_multiple_dir . '/' . basename($_SERVER['PHP_SELF'], '.php'));
		}
		if (!is_dir(dirname($publish_file))) @mkdir(dirname($publish_file));
		$file = @fopen($publish_file, 'w');
		$file_content .= "\n<!-- Published by JP7 InterAdmin in " . date('Y/m/d - H:i:s') . " -->";
		@fwrite($file, $file_content);
	}
	ob_end_flush();
}

if ($db_type) $db->Close();
else mysql_close($db);
?>
