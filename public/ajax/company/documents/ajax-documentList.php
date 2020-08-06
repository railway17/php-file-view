<?php

	include_once('../../../../library/config.php');
	
	$docType = (isset($_POST['docType']) ? $_POST['docType'] : NULL);
	$oAuth = new Auth();
	switch ($docType) {
		case 'policy':
			$relTypeID = 14;
		break;
		case 'risk':
			$relTypeID = 15;
		break;
		case 'method':
			$relTypeID = 16;
		break;
		case 'toolbox':
			$relTypeID = 17;
		break;
		case 'compliment':
			$relTypeID = 18;
		break;
		case 'coshh':
			$relTypeID = 19;
		break;
		case 'equipment':
			$relTypeID = 20;
		break;
		case 'quality':
			$relTypeID = 21;
		break;
		case 'attendance':
			$relTypeID = 36;
		break;
		case 'meeting':
			$relTypeID = 37;
		break;
		case 'operation':
			$relTypeID = 38;
		break;
		case 'certificate':
			$relTypeID = 39;
		break;
		case 'insurance':
			$relTypeID = 22;
		break;
		case 'staffmemo':
			$relTypeID = 41;
		break;
		case 'all':
			$relTypeArray = array();
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_PoliciesProcedures', 'index') == true) {
				$relTypeArray[] = 14;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_RiskAssessments', 'index') == true) {
				$relTypeArray[] = 15;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_MethodStatements', 'index') == true) {
				$relTypeArray[] = 16;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_ToolboxTalks', 'index') == true) {
				$relTypeArray[] = 17;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_Compliments', 'index') == true) {	
				$relTypeArray[] = 18;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_COSHHAssessments', 'index') == true) {
				$relTypeArray[] = 19;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_EquipmentRegister', 'index') == true) {
				$relTypeArray[] = 20;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_QualityManagementSystems', 'index') == true) {
				$relTypeArray[] = 21;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_AttendanceDocumentSignatures', 'index') == true) {
				$relTypeArray[] = 36;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_OperationalBibliography', 'index') == true) {
				$relTypeArray[] = 38;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_Certification', 'index') == true) {
				$relTypeArray[] = 39;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_MeetingMinutes', 'index') == true) {
				$relTypeArray[] = 37;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_Insurances', 'index') == true) {
				$relTypeArray[] = 22;
			}
			if($oAuth->checkPermissionAccess('adminCompanyDocuments_StaffMemos', 'index') == true) {
				$relTypeArray[] = 41;
			}
			$relTypeList = implode(',', $relTypeArray);
			
		break;
	}

	// DB table to use
	$table = DB_TBL_PREFIX.'documents';
	
	// Table's primary key
	$primaryKey = 'documentID';
	
	// Array of database columns which should be read and sent back to DataTables.
	// The db parameter represents the column name in the database, while the dt
	// parameter represents the DataTables column identifier. In this case object
	// parameter names
	
	$columns = array(
		array( 'db' => 'd.documentID', 'field' => 'documentID', 'dt' => 'dtDocumentID' ),
		array( 'db' => 'AES_DECRYPT(d.title, \''.ENCRYPTION_KEY.'\')', 'as' => 'title', 'field' => 'title', 'dt' => 'dtTitle', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'd.docCatID', 'field' => 'docCatID', 'dt' => 'dtDocCatID' ),
		array( 'db' => 'dc.name', 'as' => 'docCatName', 'field' => 'docCatName', 'dt' => 'dtDocCatName' ),
		array( 'db' => 'AES_DECRYPT(d.docFilePath, \''.ENCRYPTION_KEY.'\')', 'as' => 'docFilePath', 'field' => 'docFilePath', 'dt' => 'dtDocFilePath', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'AES_DECRYPT(d.docFileName, \''.ENCRYPTION_KEY.'\')', 'as' => 'docFileName', 'field' => 'docFileName', 'dt' => 'dtDocFileName', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'DATE_FORMAT(d.expiryDate,\'%d/%m/%Y\')', 'as' => 'expiryDate', 'field' => 'expiryDate', 'dt' => 'dtExpiryDate', 'formatter' => function( $d, $row ) { return (($d != NULL && $d != NULL) ? $d : ''); } ),
		array( 'db' => 'd.relTypeID', 'field' => 'relTypeID', 'dt' => 'dtRelTypeID' ),
		array( 'db' => 'rt.name', 'as' => 'relTypeName', 'field' => 'relTypeName', 'dt' => 'dtRelTypeName', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'd.relationID', 'field' => 'relationID', 'dt' => 'dtRelationID', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'DATE_FORMAT(d.uploadedDate,\'%d/%m/%Y\')', 'as' => 'uploadedDate', 'field' => 'uploadedDate', 'dt' => 'dtUploadedDate', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'DATE_FORMAT(d.uploadedDate,\'%H:%i:%s\')', 'as' => 'uploadedTime', 'field' => 'uploadedTime', 'dt' => 'dtUploadedTime', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'd.uploadedBy', 'field' => 'uploadedBy', 'dt' => 'dtUploadedBy', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 'db' => 'IF(u.isExternal = 1, CONCAT(u.forename, \' \', u.surname), CONCAT(AES_DECRYPT(e.forename, \''.ENCRYPTION_KEY.'\'), \' \', AES_DECRYPT(e.surname, \''.ENCRYPTION_KEY.'\')))', 'as' => 'uploadedByName', 'field' => 'uploadedByName', 'dt' => 'dtUploadedByName', 'formatter' => function( $d, $row ) { return stripslashes($d); } ),
		array( 
			'db' => 'd.documentID',
			'field' => 'documentID',
			'dt' => 'dtDownloadFile',
			'formatter' => function($d, $row){ 			
				$oAuth = new Auth();
				$oDocument = new Document();
				$dbDocument = $oDocument->getOne( $d );
				$dbDocument = $dbDocument[0];
				
				$extension = substr($dbDocument['docFileName'], strrpos($dbDocument['docFileName'], ".") + 1);
				
				$docImageSrc = '';
				if($extension == 'jpg' || $extension == 'jpeg') {
					$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/24x24/jpg.png" width="24" height="24" alt="JPG Document Icon"/>';
				} else if($extension == 'doc' || $extension == 'docx' || $extension == 'docm' || $extension == 'odt') {
					$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/24x24/doc.png" width="24" height="24" alt="DOC Document Icon"/>';
				} else if($extension == 'xls' || $extension == 'xlsx' || $extension == 'xlsxm') {
					$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/24x24/xls.png" width="24" height="24" alt="XLS Document Icon"/>';
				} else if($extension == 'ppt' || $extension == 'pptx') {
					$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/24x24/ppt.png" width="24" height="24" alt="PPT Document Icon"/>';
				} else {
					$imgFilePath = ASSETS_IMG_ROOT.'icons/files/32x32/'.$extension.'.png';
					if(file_exists($imgFilePath)) {
						$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/24x24/'.$extension.'.png" width="24" height="24" alt="'.strtoupper($extension).' Document Icon" />';
					} else {
						$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/24x24/unknown.png" width="24" height="24" alt="Unknown Document Icon" />';
					}
				}
				if($row['relTypeID'] == 14) {
					$permissionName = 'adminCompanyDocuments_PoliciesProcedures';
				} elseif($row['relTypeID'] == 15) {
					$permissionName = 'adminCompanyDocuments_RiskAssessments';
				} elseif($row['relTypeID'] == 16) {
					$permissionName = 'adminCompanyDocuments_MethodStatements';
				} elseif($row['relTypeID'] == 17) {
					$permissionName = 'adminCompanyDocuments_ToolboxTalks';
				} elseif($row['relTypeID'] == 18) {
					$permissionName = 'adminCompanyDocuments_Compliments';
				} elseif($row['relTypeID'] == 19) {
					$permissionName = 'adminCompanyDocuments_COSHHAssessments';
				} elseif($row['relTypeID'] == 20) {
					$permissionName = 'adminCompanyDocuments_EquipmentRegister';
				} elseif($row['relTypeID'] == 21) {
					$permissionName = 'adminCompanyDocuments_QualityManagementSystems';
				} elseif($row['relTypeID'] == 36) {
					$permissionName = 'adminCompanyDocuments_AttendanceDocumentSignatures';
				} elseif($row['relTypeID'] == 38) {
					$permissionName = 'adminCompanyDocuments_OperationalBibliography';
				} elseif($row['relTypeID'] == 39) {
					$permissionName = 'adminCompanyDocuments_Certification';
				} elseif($row['relTypeID'] == 22) {
					$permissionName = 'adminCompanyDocuments_Insurances';
				} elseif($row['relTypeID'] == 41) {
					$permissionName = 'adminCompanyDocuments_StaffMemos';
				} 
				
				if($oAuth->checkPermissionAccess($permissionName, 'view') == true) {
					$tdDownloadHTML = '<a href="'.ADMIN_URL.'documents/download.php?docid='.$d.'" '.(($extension == 'pdf') ? 'target="_blank"' : '').'>'.$docImageSrc.'</a>';
				} else {
					$tdDownloadHTML = '';
				}
				return $tdDownloadHTML;
			}
		),
		array( 
			'db' => 'd.documentID', 
			'field' => 'documentID', 
			'dt' => 'dtRowTools', 
			'formatter' => function( $d, $row ) 
			{ 				
				$oAuth = new Auth();
				$tdToolsHTML = $btnDeleteHTML = '';
				if($row['relTypeID'] == 14) {
					$permissionName = 'adminCompanyDocuments_PoliciesProcedures';
				} elseif($row['relTypeID'] == 15) {
					$permissionName = 'adminCompanyDocuments_RiskAssessments';
				} elseif($row['relTypeID'] == 16) {
					$permissionName = 'adminCompanyDocuments_MethodStatements';
				} elseif($row['relTypeID'] == 17) {
					$permissionName = 'adminCompanyDocuments_ToolboxTalks';
				} elseif($row['relTypeID'] == 18) {
					$permissionName = 'adminCompanyDocuments_Compliments';
				} elseif($row['relTypeID'] == 19) {
					$permissionName = 'adminCompanyDocuments_COSHHAssessments';
				} elseif($row['relTypeID'] == 20) {
					$permissionName = 'adminCompanyDocuments_EquipmentRegister';
				} elseif($row['relTypeID'] == 21) {
					$permissionName = 'adminCompanyDocuments_QualityManagementSystems';
				} elseif($row['relTypeID'] == 36) {
					$permissionName = 'adminCompanyDocuments_AttendanceDocumentSignatures';
				} elseif($row['relTypeID'] == 38) {
					$permissionName = 'adminCompanyDocuments_OperationalBibliography';
				} elseif($row['relTypeID'] == 39) {
					$permissionName = 'adminCompanyDocuments_Certification';
				} elseif($row['relTypeID'] == 22) {
					$permissionName = 'adminCompanyDocuments_Insurances';
				} elseif($row['relTypeID'] == 41) {
					$permissionName = 'adminCompanyDocuments_StaffMemos';
				}
				
				if($oAuth->checkPermissionAccess($permissionName, 'delete') == true) {
					$btnDeleteHTML = '<a href="javascript:void(0);" class="deleteDocument" data-toggle="tooltip" data-placement="left" title="Delete Document"><i class="fal fa-lg fa-trash-alt"></i></a>';
				}
				$tdToolsHTML = $btnDeleteHTML;
				return $tdToolsHTML;
			}
		)
	);
			
	$cJoinSQL = "FROM {$table} AS d LEFT JOIN ".DB_TBL_PREFIX."documentcategories AS dc ON dc.docCatID = d.docCatID LEFT JOIN ".DB_TBL_PREFIX."reltypes AS rt ON rt.relTypeID = d.relTypeID LEFT JOIN ".DB_TBL_PREFIX."users AS u ON u.userID = d.uploadedBy LEFT JOIN ".DB_TBL_PREFIX."employees AS e ON e.userID = d.uploadedBy AND u.isExternal = 0";	
	if($docType == 'all') {
		$cWhereSQL = "d.relTypeID IN(".$relTypeList.") AND d.isDeleted = 0";
	} else {
		$cWhereSQL = "d.relTypeID = ".$relTypeID." AND d.isDeleted = 0";
	}
	$cGroupBySQL = "";
				 
	$oSSP = new SSP();
	echo json_encode(
		$oSSP->simple( $_POST, $table, $primaryKey, $columns, $cJoinSQL, $cWhereSQL, $cGroupBySQL )
	);

?>