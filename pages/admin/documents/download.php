<?php 

	require_once('../../../library/config.php');	

	if ($oAuth->checkLoginStatus() == true) {
		$docID = isset($_GET['docid']) ? $_GET['docid'] : NULL;
		$relTypeID = isset($_GET['reltypeid']) ? $_GET['reltypeid'] : NULL;
		$relID = isset($_GET['relid']) ? $_GET['relid'] : NULL;
		$appID = isset($_GET['appid']) ? $_GET['appid'] : NULL;
		$location = isset($_GET['location']) ? $_GET['location'] : NULL;
		$fileName = isset($_GET['filename']) ? $_GET['filename'] : NULL;
		$taskOrderID = isset($_GET['taskorderid']) ? $_GET['taskorderid'] : NULL;
		
		if($docID != NULL) {
			$oDocument = new Document();
			$dbDocument = $oDocument->getOne($docID);
			$dbDocument = $dbDocument[0];
			$docFileName = $dbDocument['docFileName'];
			$docFilePath = $dbDocument['docFilePath'];
		} 
		if($relTypeID != NULL) {
			switch($relTypeID) {
				case 4:
					$oVehicleDefect = new VehicleDefect();
					$dbVehicleDefect = $oVehicleDefect->getOne($relID);
					$dbVehicleDefect = $dbVehicleDefect[0];
					
					$docFilePath = $dbVehicleDefect['defectRptFilePath'];
					$docFileName = $dbVehicleDefect['defectRptFileName'];
				break;		
				case 5:
					$oVehicleService = new VehicleService();
					$dbVehicleService = $oVehicleService->getOne($relID);
					$dbVehicleService = $dbVehicleService[0];
					
					$docFilePath = $dbVehicleService['serviceRptFilePath'];
					$docFileName = $dbVehicleService['serviceRptFileName'];
				break;		
				case 6:
					$oVehicleEvent = new VehicleEvent();
					$dbVehicleEvent = $oVehicleEvent->getOne($relID);
					$dbVehicleEvent = $dbVehicleEvent[0];
					
					$docFilePath = $dbVehicleEvent['eventRptFilePath'];
					$docFileName = $dbVehicleEvent['eventRptFileName'];
				break;		
				case 11:
					$oTrailerDefect = new TrailerDefect();
					$dbTrailerDefect = $oTrailerDefect->getOne($relID);
					$dbTrailerDefect = $dbTrailerDefect[0];
					
					$docFilePath = $dbTrailerDefect['defectRptFilePath'];
					$docFileName = $dbTrailerDefect['defectRptFileName'];
				break;	
				case 68:
					$oTrailerService = new TrailerService();
					$dbTrailerService = $oTrailerService->getOne($relID);
					$dbTrailerService = $dbTrailerService[0];
					
					$docFilePath = $dbTrailerService['serviceRptFilePath'];
					$docFileName = $dbTrailerService['serviceRptFileName'];
				break;	
				case 61:
					$oApplicant = new Applicant();
					$dbApplicant = $oApplicant->getOne($relID);
					$dbApplicant = $dbApplicant[0];
					
					$docFilePath = $dbApplicant['cvDocumentFilePath'];
					$docFileName = $dbApplicant['cvDocumentFileName'];
				break;	
			}
		}
		if($appID != NULL) {
			$oLocalAuthApp = new LocalAuthorityApplication();
			$dbLocalAuthApp = $oLocalAuthApp->getOne($appID);
			$dbLocalAuthApp = $dbLocalAuthApp[0];
			
			$docFilePath = $dbLocalAuthApp['docFilePath'];
			$docFileName = $dbLocalAuthApp['docFileName'];
		} 
		if($taskOrderID != NULL) {
			$oCustTaskOrder = new CustTaskOrder();
			$dbCustTaskOrder = $oCustTaskOrder->getOne($taskOrderID);
			$dbCustTaskOrder = $dbCustTaskOrder[0];
			
			$docFileName = $dbCustTaskOrder['docFileName'];
			$docFilePath = $dbCustTaskOrder['docFilePath'];
		} 
		
		if($fileName != NULL) {
            switch($location ) {
                case 'reports':
                    $docFileName = base64_url_decode($fileName);
				    $outputFile = REPORTS_ROOT.$docFileName;
                    break;
                case 'uploads':
                    $docFileName = base64_url_decode($fileName);
				    $outputFile = UPLOADS_ROOT.$docFileName;
                    break;
                case 'signatures':
                    $docFileName = base64_url_decode($fileName);
				    $outputFile = UPLOADS_ROOT.'signatures/'.$docFileName;
                    break;
                case 'temp':
                    $docFileName = base64_url_decode($fileName);
				    $outputFile = TEMP_ROOT.$docFileName;
                    break;
                case 'NETWORK_RAIL_ROOT':
                    $docFileName = base64_url_decode($fileName);
				    $outputFile = NETWORK_RAIL_ROOT.$docFileName;
                    break;
                case 'KIER_ROOT':
                    $docFileName = base64_url_decode($fileName);
				    $outputFile = KIER_ROOT.$docFileName;
                    break;
                case 'COUNCIL_APPS_ROOT':
                    $docFileName = base64_url_decode($fileName);
				    $outputFile = COUNCIL_APPS_ROOT.$docFileName;
                    break;
                case 'LOCAL_OPERATIONS_ROOT':
                    $docFileName = base64_url_decode($fileName);
				    $outputFile = LOCAL_OPERATIONS_ROOT.$docFileName;
                    break;
                default: 
                    $docFileName = $fileName;
                    $docFilePath = TEMP_ROOT;
                    $outputFile = TEMP_ROOT.$fileName;
                    break;
            }
            
		} else {
			$outputFile = UPLOADS_ROOT.$docFilePath.$docFileName;
			//echo $outputFile;
		}
		if(file_exists($outputFile)) {
			$fh = fopen($outputFile, "r");							
			$fileContent = fread($fh, filesize($outputFile));
			$fileSize = filesize($outputFile);
			
			//$finfo = finfo_open(FILEINFO_MIME);
			$fileType = filetype($outputFile);
			//finfo_close($finfo);
			
			header("Content-Length: {$fileSize}");
			$extension = substr($docFileName, strrpos($docFileName, ".") + 1);

			if($extension == 'pdf') {
				header("Content-Type: application/pdf; charset=binary");
				header('Content-Disposition: inline; filename="'.$docFileName.'"');
				header('Content-Transfer-Encoding: binary');
				header('Accept-Ranges: bytes');
				header('Expires: 0');
				header('Cache-Control: public, must-revalidate, max-age=0');
			} else {
				header("Content-Type: {$fileType}");
				header("Content-Disposition: attachment; filename={$docFileName}");
			}
			echo $fileContent;
            if($location == 'temp') {
                fclose($fh);
				unlink($outputFile);
			}
		}
		else {
			echo '<h1>File Not Found!</h1>';
			echo '<p>Sorry the document you have requested to view / download may have been restricted, moved or deleted. Please refer to the System Administrator for assistance.</p>';
		}
	}
	else {
		$oTemplate->setAlert('Unauthorized Access - Please login to access the document!', 'error');
		$oTemplate->redirect(SITE_URL.'pages/login.php');
	}

?>