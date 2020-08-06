<?php

	require_once('../../library/config.php');
    $relTypeID = (isset($_POST['reltypeid']) ? $_POST['reltypeid'] : NULL);
    $recordID = (isset($_POST['recordid']) ? $_POST['recordid'] : NULL);
    $action = (isset($_POST['action']) ? $_POST['action'] : NULL);
    $action = (isset($_POST['action']) ? $_POST['action'] : NULL);
	
	$oRecordActivity = new RecordActivity();

	if ($action == 'updateRecordTime'){
		$recordActivityData = array(
			'timestamp' => time(),
			'sessionID' => $_SESSION['sessionID']
		);
		$rstRecAct = $oRecordActivity->update($recordActivityData,$relTypeID,$recordID);
		echo $rstRecAct;
	} elseif ($action == 'deleteRecordLock'){
		$rstRecAct = $oRecordActivity->delete($relTypeID,$recordID);
		var_dump($rstRecAct);
	}
?>