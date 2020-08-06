<?php

	require_once('../../../library/config.php');

	if ($oAuth->checkLoginAccess() == true) 
	{	
		$oTemplate->setPageID('lock-page');
		
		$oTemplate->setInclude(ASSETS_CSS_URL.'lockscreen.min.css','cssInclude');
		
		$oTemplate->setMetaData('System Lock','pageTitle');
		$oTemplate->load(ADMIN_VIEWS.'v_lock.php');
	}

?>