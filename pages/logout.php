<?php

	require_once('../library/config.php');
	
	$oActionLog->insert($_SESSION['fullname'].' has logged out of the System', '');
	$footerCode = '
	<script type="text/javascript">
		$(function() {
			sessionStorage.clear();
		});
	</script>';
	$oTemplate->setInclude($footerCode,'footerCode');
	
	$deferURL = isset($_GET['defer']) ? $_GET['defer'] : NULL;

	$oAuth->logout();
	$oTemplate->setAlert('Successfully logged out!');
	if($deferURL != NULL) {
		$oTemplate->redirect(SITE_URL.'pages/login.php?defer='.$deferURL);
	} else {
		$oTemplate->redirect(SITE_URL.'pages/login.php');
	}
	
?>