<?php
include_once 'sys/boot.php';


// Check access. This script should be available only from the admin panel
if (empty($_SESSION['adm_panel_authorize']) ||
    $_SESSION['adm_panel_authorize'] < time() ||
    empty($_SESSION['user'])
) {
    die('Access denied');
} else if (!empty($_SESSION['adm_panel_authorize'])) {
    $_SESSION['adm_panel_authorize'] = (time() + Config::read('session_time', 'secure'));
}


// files storage folder
$dir = ROOT . '/sys/files/pages/';
if (!file_exists($dir)) mkdir($dir, 0777, true);


$_FILES['file']['type'] = strtolower($_FILES['file']['type']);


if ($_FILES['file']['type'] == 'image/png'
|| $_FILES['file']['type'] == 'image/jpg'
|| $_FILES['file']['type'] == 'image/gif'
|| $_FILES['file']['type'] == 'image/jpeg'
|| $_FILES['file']['type'] == 'image/pjpeg')
{	
	// setting file's mysterious name
	$filename = md5(date('YmdHis')).'.jpg';
	$file = $dir.$filename;
	 
	// copying
	copy($_FILES['file']['tmp_name'], $file);
	 
	// displaying file
	$array = array(
		'filelink' => WWW_ROOT . '/sys/files/pages/' . $filename,
	);
	echo stripslashes(json_encode($array));
}