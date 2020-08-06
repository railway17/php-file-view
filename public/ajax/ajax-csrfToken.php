<?php

	require_once('../../library/config.php');

	$csrfToken = NULL;
	if(isset($_REQUEST['csrftoken']))
	{
		$csrfToken = $_REQUEST['csrftoken'];
	}
	$action = NULL;
	if(isset($_REQUEST['action']))
	{
		$action = $_REQUEST['action'];
	}
	
	$oRecordActivity = new RecordActivity();
	if ( $action == 'deleteToken' ){
		if($csrfToken != NULL) {
			delete_csrf_token($csrfToken);
		}
	} 

?>