 <?php
	require_once('../../../library/config.php');

	if ($oAuth->checkLoginStatus('adminDashboard', 'index') == true) {												
		$oTemplate->setMetaData('Dashboard','pageTitle');		
		$oTemplate->load(ADMIN_VIEWS.'dashboard/v_index.php');
	}

?>