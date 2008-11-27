<?
// Verifica se esta postando algo
if (!$_POST) {
	header('Location:' . $c_url);
	exit;
}

// Define o arquivo de template do e-mail
if ($_POST['template'] == 'global' or $_POST['template'] == false) {
	$template = jp7_path_find('../../_default/site/_templates/sendFriend/mail.htm');
} else {
	$template = jp7_path_find('../site/_templates/' . $_POST['template']);
}

$template = file_get_contents($template);

$template = str_replace('%curl%', $c_url, $template);
$template = str_replace('%cname%', $c_site, $template);
$template = str_replace('%to%', $_POST['to'], $template);
$template = str_replace('%name%', $_POST['name'], $template);
$template = str_replace('%mail%', $_POST['mail'], $template);
$template = str_replace('%url%', $_POST['url'], $template);
$template = str_replace('%comment%', $_POST['comment'], $template);

$headers = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'To: ' . $_POST['to'] . "\r\n";
$headers .= 'From: ' . $_POST['name'] . ' <' . $_POST['mail'] . '>' . "\r\n";

if (mail($_POST['to'], 'Indicação do site', $template, $headers)) {
	if ($_POST['success'] == 'global' or $_POST['success'] == false) {
		$template = jp7_path_find('../../_default/site/_templates/sendFriend/success.htm');
	} else {
		$template = jp7_path_find('../site/_templates/' . $_POST['success']);
	}
} else {
	if ($_POST['fail'] == 'global' or $_POST['fail'] == false) {
		$template = jp7_path_find('../../_default/site/_templates/sendFriend/fail.htm');
	} else {
		$template = jp7_path_find('../site/_templates/' . $_POST['fail']);

		// Verifica se o template existe
		if (!file_exists($template)) {
			header('Location:../../');
			exit;
		}
	}
}

// Escreve o template
echo file_get_contents($template);
?>
