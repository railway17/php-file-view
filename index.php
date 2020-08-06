<?php
	
	include_once('library/config.php');
	$ROOT_PATH = '/file';
    $requestURL = strtok($_SERVER["REQUEST_URI"], '?');

	// if ($oAuth->checkLoginStatus() == true) {
	// 	$oTemplate->redirect(ADMIN_URL.'dashboard/index.php');
	// } elseif($_SESSION['awaitingVerifCode'] == true){
	// 	$oTemplate->redirect(SITE_URL.'pages/verify.php');
	// } else {
	// 	// $oTemplate->redirect(SITE_URL.'pages/login.php');
		// $oTemplate->redirect(SITE_URL.'pages/documents/index.php');
	// }		
		
	if ($requestURL == $ROOT_PATH.'/documents') {
        require_once('pages/documents/documents.php');
    } else if($requestURL == $ROOT_PATH.'/folders') {
        require_once('pages/documents/folders.php');
    }

?>
