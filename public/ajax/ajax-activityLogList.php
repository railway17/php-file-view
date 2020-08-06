<?php

    include_once('../../library/config.php');
    $relationID = (isset($_POST['relationid']) ? $_POST['relationid'] : NULL);
	$relTypeID = (isset($_POST['reltypeid']) ? $_POST['reltypeid'] : NULL);

    // DB table to use
    $table = DB_TBL_PREFIX.'actionlogs';

    // Table's primary key
    $primaryKey = 'logID';

    // Array of database columns which should be read and sent back to DataTables.
    // The db parameter represents the column name in the database, while the dt
    // parameter represents the DataTables column identifier. In this case object
    // parameter names
    $columns = array(
        array( 'db' => 'logID', 'field' => 'logID', 'dt' => 'dtLogID' ),
        array( 'db' => 'DATE_FORMAT(al.dateTime,\'%d/%m/%Y %H:%i:%s\')', 'as' => 'dateTime', 'field' => 'dateTime', 'dt' => 'dtDateTime' ),
        array( 'db' => 'u.username', 'field' => 'username', 'dt' => 'dtUsername' ),
        array( 'db' => 'action', 'field' => 'action', 'dt' => 'dtAction' )
    );

    $cJoinSQL = "FROM {$table} AS al LEFT JOIN ".DB_TBL_PREFIX."users AS u ON u.userID = al.userID";
    $cWhereSQL = "1=1";
    if($_SESSION['companyID'] == 1 && $_SESSION['depotID'] != 1) {	
        $cWhereSQL .= " AND u.depotID = ".$_SESSION['depotID'];
    }
    if($relTypeID != NULL && $relationID != NULL) {
		$cWhereSQL .= " AND al.relTypeID = ".$relTypeID." AND al.relationID = ".$relationID;
	} else {
        $cWhereSQL .= " AND al.dateTime > DATE_SUB(NOW(), INTERVAL 3 MONTH)";     
    }
   
    $cGroupBySQL = "";

    $oSSP = new SSP();
    echo json_encode(
        $oSSP->simple( $_POST, $table, $primaryKey, $columns, $cJoinSQL, $cWhereSQL, $cGroupBySQL)
    );

?>