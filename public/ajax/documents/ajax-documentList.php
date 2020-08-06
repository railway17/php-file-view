<?php

	include_once('../../../library/config.php');

	$docType = (isset($_POST['doctype']) ? $_POST['doctype'] : "");
	$relTypeID = (isset($_POST['reltypeid']) ? $_POST['reltypeid'] : NULL);
	$relationID = (isset($_POST['relationid']) ? $_POST['relationid'] : NULL);
	$companyID = (isset($_POST['companyid']) ? $_POST['companyid'] : NULL);
    $fString = (isset($_POST['fString']) ? $_POST['fString'] : NULL);
	$fYearVal = (isset($_POST['fYear']) ? $_POST['fYear'] : NULL);

	// DB table to use
	$table = DB_TBL_PREFIX.'documents';
	
	// Table's primary key
	$primaryKey = 'documentID';
	
	// Array of database columns which should be read and sent back to DataTables.
	// The db parameter represents the column name in the database, while the dt
	// parameter represents the DataTables column identifier. In this case object
	// parameter names
	
	$columns = array(
		array( 'db' => 'd.displayOrder', 'as' => 'displayOrder', 'field' => 'displayOrder', 'dt' => 'dtDisplayOrder' ),
        array( 'db' => 'd.displayOrder', 'as' => 'displayOrderIcon', 'field' => 'displayOrderIcon', 'dt' => 'dtDisplayOrderIcon', 'formatter' => function( $d, $row ) { 
            $displayOrderHTML = '<a href="javascript:void(0);" class="moveDocument" data-toggle="tooltip" data-placement="left" title=""><i class="fal fa-lg fa-arrows"></i></a>';
            return $displayOrderHTML;
        } ),
		array( 'db' => 'd.documentID', 'field' => 'documentID', 'dt' => 'dtDocumentID' ),
		array( 'db' => 'AES_DECRYPT(d.title, \''.ENCRYPTION_KEY.'\')', 'as' => 'title', 'field' => 'title', 'dt' => 'dtTitle', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'd.docCatID', 'field' => 'docCatID', 'dt' => 'dtDocCatID' ),
		array( 'db' => 'd.isCustomer', 'field' => 'isCustomer', 'dt' => 'dtIsCustomer', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'dc.name', 'as' => 'docCatName', 'field' => 'docCatName', 'dt' => 'dtDocCatName', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'AES_DECRYPT(d.docFilePath, \''.ENCRYPTION_KEY.'\')', 'as' => 'docFilePath', 'field' => 'docFilePath', 'dt' => 'dtDocFilePath', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'AES_DECRYPT(d.docFileName, \''.ENCRYPTION_KEY.'\')', 'as' => 'docFileName', 'field' => 'docFileName', 'dt' => 'dtDocFileName', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'AES_DECRYPT(d.docFileName, \''.ENCRYPTION_KEY.'\')', 'as' => 'extension', 'field' => 'extension', 'dt' => 'dtExtension', 'formatter' => function( $d, $row ) { return strtoupper(substr($d, strrpos($d, ".") + 1)); } ),
		array( 'db' => 'IF(d.isConfidential = 1, \'Yes\' , \'No\' )', 'as' => 'isConfidential', 'field' => 'isConfidential', 'dt' => 'dtIsConfidential' ),
		array( 'db' => 'IF(d.isCADPlan = 1, \'Yes\' , \'No\' )', 'as' => 'isCADPlan', 'field' => 'isCADPlan', 'dt' => 'dtIsCADPlan'),
		array( 'db' => 'IF(d.includeVanPack = 1, \'Yes\' , \'No\' )', 'as' => 'includeVanPack', 'field' => 'includeVanPack', 'dt' => 'dtIncludeVanPack'),
		array( 'db' => 'd.relTypeID', 'field' => 'relTypeID', 'dt' => 'dtRelTypeID' ),
		array( 'db' => 'rt.name', 'as' => 'relTypeName', 'field' => 'relTypeName', 'dt' => 'dtRelTypeName', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'd.relationID', 'field' => 'relationID', 'dt' => 'dtRelationID' ),
		array( 'db' => 'd.localAuthAppTypeID', 'field' => 'localAuthAppTypeID', 'dt' => 'dtLocalAuthAppTypeID' ),
		array( 'db' => 'laat.name', 'as' => 'localAuthAppTypeName', 'field' => 'localAuthAppTypeName', 'dt' => 'dtLocalAuthAppTypeName', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'DATE_FORMAT(d.expiryDate,\'%d/%m/%Y\')', 'as' => 'expiryDate', 'field' => 'expiryDate', 'dt' => 'dtExpiryDate', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'DATE_FORMAT(d.uploadedDate,\'%d/%m/%Y\')', 'as' => 'uploadedDate', 'field' => 'uploadedDate', 'dt' => 'dtUploadedDate', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'DATE_FORMAT(d.uploadedDate,\'%H:%i:%s\')', 'as' => 'uploadedTime', 'field' => 'uploadedTime', 'dt' => 'dtUploadedTime', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'd.uploadedBy', 'field' => 'uploadedBy', 'dt' => 'dtUploadedBy', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'IF(d.isCustomer = 1, CONCAT(cl.forename, \' \', cl.surname), IF(u.isExternal = 1, CONCAT(u.forename, \' \', u.surname), CONCAT(AES_DECRYPT(e.forename, \''.ENCRYPTION_KEY.'\'), \' \', AES_DECRYPT(e.surname, \''.ENCRYPTION_KEY.'\'))))', 'as' => 'uploadedByName', 'field' => 'uploadedByName', 'dt' => 'dtUploadedByName', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 
			'db' => 'd.documentID',
			'field' => 'documentID',
			'dt' => 'dtDownloadFile',
			'formatter' => function( $d, $row ) { 				
				$oAuth = new Auth();
				$tdDownloadHTML = '';
				
				$extension = substr($row['docFileName'], strrpos($row['docFileName'], ".") + 1);
				
				$documentRelTypes = array('14', '15', '16', '17', '18', '19', '20', '21', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '41','69');
                if (in_array($row['relTypeID'], $documentRelTypes)){
                    if($oAuth->checkPermissionAccess('adminCompanyDocuments'.$row['relTypeID'], 'view') == true || $oAuth->checkPermissionAccess('adminCompanyAudits'.$row['relTypeID'], 'view') == true) {
                        $tdDownloadHTML = '<a data-toggle="tooltip" data-placement="left" title="Download Document" href="'.ADMIN_URL.'documents/download.php?docid='.$d.'"  '.(($extension == 'pdf') ? 'target="_blank"' : '').'><i class="fal fa-lg fa-download"></i></a>';
                    }
                } else {
                    $tdDownloadHTML = '<a data-toggle="tooltip" data-placement="left" title="Download Document" href="'.ADMIN_URL.'documents/download.php?docid='.$d.'"><i class="fal fa-lg fa-download"></i></a>';
                }
                return $tdDownloadHTML;
            }
		),
        array( 
			'db' => 'd.documentID', 
			'field' => 'documentID', 
			'dt' => 'dtRowTools', 
			'formatter' => function( $d, $row ) { 		
				$oAuth = new Auth();
				$tdToolsHTML = $btnEditHTML = $btnDeleteHTML = $btnDownloadHTML = '';
                $documentRelTypes = array('14', '15', '16', '17', '18', '19', '20', '21', '22', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '41','69');
				
				$extension = substr($row['docFileName'], strrpos($row['docFileName'], ".") + 1);
				
				if (in_array($row['relTypeID'], $documentRelTypes)){
                    if($oAuth->checkPermissionAccess('adminCompanyDocuments'.$row['relTypeID'], 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyAudits'.$row['relTypeID'], 'edit') == true) {
                        $btnEditHTML = ' <a href="javascript:void(0);" class="editDocument" data-toggle="tooltip" data-placement="left" title="Edit Document Information"><i class="fal fa-lg fa-edit"></i></a>';
                    }
                    if($oAuth->checkPermissionAccess('adminCompanyDocuments'.$row['relTypeID'], 'delete') == true || $oAuth->checkPermissionAccess('adminCompanyAudits'.$row['relTypeID'], 'delete') == true) {
                        $btnDeleteHTML = ' <a href="javascript:void(0);" class="deleteDocument" data-toggle="tooltip" data-placement="left" title="Delete Document Information"><i class="fal fa-lg fa-trash-alt"></i></a>';
                    }
					if($oAuth->checkPermissionAccess('adminCompanyDocuments'.$row['relTypeID'], 'view') == true || $oAuth->checkPermissionAccess('adminCompanyAudits'.$row['relTypeID'], 'view') == true) {
                        $btnDownloadHTML = '<a data-toggle="tooltip" data-placement="left" title="Download Document" href="'.ADMIN_URL.'documents/download.php?docid='.$d.'"  '.(($extension == 'pdf') ? 'target="_blank"' : '').'><i class="fal fa-lg fa-download"></i></a>';
                    }
                } else {
					$btnEditHTML = ' <a href="javascript:void(0);" class="editDocument" data-toggle="tooltip" data-placement="left" title="Edit Document Information"><i class="fal fa-lg fa-edit"></i></a>';
					$btnDeleteHTML = ' <a href="javascript:void(0);" class="deleteDocument" data-toggle="tooltip" data-placement="left" title="Delete Document Information"><i class="fal fa-lg fa-trash-alt"></i></a>';
					$btnDownloadHTML = '<a data-toggle="tooltip" data-placement="left" title="Download Document" href="'.ADMIN_URL.'documents/download.php?docid='.$d.'"  '.(($extension == 'pdf') ? 'target="_blank"' : '').'><i class="fal fa-lg fa-download"></i></a>';
                }
				$tdToolsHTML = $btnDownloadHTML.' '.$btnEditHTML.' '.$btnDeleteHTML;
				return $tdToolsHTML; 
			} 
		)
	);
			
	$cJoinSQL = "FROM {$table} AS d LEFT JOIN ".DB_TBL_PREFIX."documentcategories AS dc ON dc.docCatID = d.docCatID LEFT JOIN ".DB_TBL_PREFIX."reltypes AS rt ON rt.relTypeID = d.relTypeID LEFT JOIN ".DB_TBL_PREFIX."users AS u ON u.userID = d.uploadedBy LEFT JOIN ".DB_TBL_PREFIX."employees AS e ON e.userID = d.uploadedBy AND u.isExternal = 0 LEFT JOIN ".DB_TBL_PREFIX."customerlogins AS cl ON cl.loginID = d.uploadedBy LEFT JOIN ".DB_TBL_PREFIX."localauthorityapplicationtypes AS laat ON laat.localAuthAppTypeID = d.localAuthAppTypeID";	
	
	$cWhereSQL = "1=1";
    if($fYearVal != NULL && $fYearVal > 0) {
        $cWhereSQL = "YEAR(d.uploadedDate) = '".$fYearVal."' AND d.isDeleted = 0";
    } else {
        $cWhereSQL = "d.isDeleted = 0";
    }
    if($companyID != NULL) {
		$cWhereSQL .= " AND d.companyID = ".$companyID;
	}
	if($relTypeID != NULL) {
		$cWhereSQL .= " AND d.relTypeID = ".$relTypeID;
	}
	if($relationID != NULL) {
		$cWhereSQL .= " AND d.relationID = ".$relationID;
	}
	if($docType == 'company') {
		$cWhereSQL .= " AND d.relTypeID IN (14, 15, 16, 17, 18, 19, 20, 21, 22, 36, 37, 38, 39, 41, 69)";
	} elseif($docType == 'audits') {
		$cWhereSQL .= " AND d.relTypeID IN (30, 31, 32, 33, 34, 35)";
	}
	
	$cGroupBySQL = "";
				 
	$oSSP = new SSP();
	echo json_encode($oSSP->simple( $_POST, $table, $primaryKey, $columns, $cJoinSQL, $cWhereSQL, $cGroupBySQL ));
?>