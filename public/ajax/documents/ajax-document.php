<?php

	include_once('../../../library/config.php');
	
	$action = isset($_POST['action']) ? $_POST['action'] : NULL;
	$companyID = isset($_POST['companyid']) ? $_POST['companyid'] : 0;
	$relTypeID = isset($_POST['reltypeid']) ? $_POST['reltypeid'] : 0;
	$relationID = isset($_POST['relid']) ? $_POST['relid'] : 0;
	$employeeID = isset($_POST['employeeid']) ? $_POST['employeeid'] : NULL;
	$postData = isset($_POST['postdata']) ? $_POST['postdata'] : NULL;
	$docID = isset($_POST['docid']) ? $_POST['docid'] : 0;
	$docCatID = isset($_POST['doccatid']) ? $_POST['doccatid'] : 0;
    $newDisplayOrder = isset($_POST['newdisplayorder']) ? $_POST['newdisplayorder'] : NULL;
	$delRows = isset($_POST['delrows']) ? $_POST['delrows'] : NULL;
	
	$oDocument = new Document();

    if($relTypeID != NULL)  {
		switch($relTypeID) {
			case 1:
			case 2:
				$oWork = new Work();
				$dbWork = $oWork->getOne($relationID);
				$dbWork = $dbWork[0];
				$workTypeID = $dbWork['workTypeID'];
			break;
			case 3:
				$oVehicle = new Vehicle();
				$dbVehicle = $oVehicle->getOne($relationID);
				$dbVehicle = $dbVehicle[0];
			break;
			case 7:
				$oEmployee = new Employee();
				$dbEmployee = $oEmployee->getOne($relationID);
				$dbEmployee = $dbEmployee[0];
			break;
			case 9:
				$oTrailer = new Trailer();
				$dbTrailer = $oTrailer->getOne($relationID);
				$dbTrailer = $dbTrailer[0];
			break;
			case 10:
				$oQuote = new Quote();
				$dbQuote = $oQuote->getOne($relationID);
				$dbQuote = $dbQuote[0];
			break;
			case 13:
				$oLocalAuth = new LocalAuthority();
				$dbLocalAuth = $oLocalAuth->getOne($relationID);
				$dbLocalAuth = $dbLocalAuth[0];
			break;
			case 40:
				$oWork = new Work();
				$dbWork = $oWork->getOne($relationID);
				$dbWork = $dbWork[0];
			break;
			case 43:
				$oCustomer = new Customer();
				$dbCustomer = $oCustomer->getOne($relationID);
				$dbCustomer = $dbCustomer[0];
			break;
		}
	}
	
	$oRelType = new RelType();
	$dbRelType = $oRelType->getOne($relTypeID);
	$dbRelType = $dbRelType[0];
	
	if($action == 'refreshDocumentList'){
        $oDocument = new Document();
        $documentList = $oDocument->getAll( "AND d.relTypeID = ".$relTypeID." AND d.relationID = ".$relationID );
        echo json_encode(multiArrayMap('stripslashes', $documentList), JSON_UNESCAPED_UNICODE);
    } elseif ($action == 'getCategoryList') {
		$oDocument = new Document();
		$dbDocument = $oDocument->getOne($docID);
       	$oDocCat = new DocumentCategory();
		$docCatList = $oDocCat->getAll( "AND relTypeID = ".$dbDocument[0]['relTypeID'] );
		echo json_encode(multiArrayMap('stripslashes', $docCatList), JSON_UNESCAPED_UNICODE);
	} else if( $action == 'getFileData' ) {
		$dbDocument = $oDocument->getOne( $docID );
		$dbDocument = $dbDocument[0];
		echo json_encode(multiArrayMap('stripslashes',$dbDocument), JSON_UNESCAPED_UNICODE);
	} elseif($action == 'uploadFiles') {
		
		$fileUploadPath = date('Y-m-d')."/".generateRandomString()."/";
		
		$fileUploadDirPath = UPLOADS_ROOT.$fileUploadPath;
		if (!file_exists($fileUploadDirPath)) {
			mkdir($fileUploadDirPath, 0777, true);
		}
		
		if (!empty($_FILES)) {
			$tempFile = $_FILES['Filedata']['tmp_name'];
			$fileName = $_FILES['Filedata']['name'];
			$errorCode = $_FILES['Filedata']['error'];
			
			$baseFileName = preg_replace('/(.*)\.([^.]+)$/','\\1',$fileName);
			$extension = substr($fileName, strrpos($fileName, ".") + 1);
			
			$newFileName = generateRandomString(32).'.'.$extension;
			
			$defaultFileTitle = trim($baseFileName);
			$defaultFileTitle = str_replace("-"," ",$defaultFileTitle);
			$defaultFileTitle = str_replace(","," ",$defaultFileTitle);
			$defaultFileTitle = str_replace("_"," ",$defaultFileTitle);
			$defaultFileTitle = str_replace("  "," ",$defaultFileTitle);
			$defaultFileTitle = str_replace("   "," ",$defaultFileTitle);
			$defaultFileTitle = sqlEscape($defaultFileTitle);
			
			$targetFile = rtrim($fileUploadDirPath,'/').'/'.$newFileName;
			
			$fileTypes = array('jpg','jpeg','gif','png','bmp','rtf','doc','docx','docm','odt','xls','xlsx','xlsm','msg','pdf','wav','mp3', 'pptx');
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			
			if (in_array(strtolower($fileParts['extension']), $fileTypes)) {	
				move_uploaded_file($tempFile, $targetFile);
				if(file_exists($targetFile)) {
					$index = 0;
					if($relationID > 0) {
						$documentList = $oDocument->getAll( "AND d.relTypeID = ".$relTypeID." AND d.relationID = ".$relationID );
					} else {
						$documentList = $oDocument->getAll( "AND d.relTypeID = ".$relTypeID );
					}

					if($documentList){
						$index = count($documentList) + 1;
					}

					$documentArrayData = array(
						'companyID' => (($companyID > 0) ? filter_var($companyID, FILTER_SANITIZE_NUMBER_INT) : 0),
						'docFilePath' => $fileUploadPath,
						'docFileName' => $newFileName,
						'title' => $defaultFileTitle,
						'docCatID' => (($docCatID > 0) ? filter_var($docCatID, FILTER_SANITIZE_NUMBER_INT) : 0),
						'relTypeID' => $relTypeID,
						'relationID' => $relationID,
						'displayOrder' => $index,
						'isCADPlan' => 0,
						'isConfidential' => 0,
						'includeVanPack' => 0,
						'uploadedDate' => date('Y-m-d H:i:s'),
						'uploadedBy' => $_SESSION['userID']
					);

					$columnArray=array();
					$rowArray = array();
					$encryptArray = array(
						'docFileName',
						'docFilePath',
						'title'
					);

					foreach ($documentArrayData as $documentData => $value) {
						array_push($columnArray, $documentData);

						if (!in_array($documentData, $encryptArray) || $value == '') {
							if ($value == '') {
								$value = 'NULL';
							}
							if (is_numeric($value)) {
								array_push($rowArray, $value);
							} else {
								array_push($rowArray, "'".$value."'");
							}
						} else {
							array_push($rowArray, "AES_ENCRYPT('".$value."', '".ENCRYPTION_KEY."')");
						}
					}
					$column = implode(", ", $columnArray);
					$row = implode(", ", $rowArray);
					$sql = "INSERT INTO ".DB_NAME.'.'.DB_TBL_PREFIX."documents (".$column.") VALUES (".$row.");";
					$sql = str_replace("'NULL'", 'NULL', $sql);

                    $rstDocument = $oDocument->customSQL($sql, true);
                    $documentID = $rstDocument['id'];
					if($documentID > 0) {
						$oActionLog->insert($_SESSION['fullname'].' has added '.$defaultFileTitle.' document ('.$documentID.') relating to '.$dbRelType['name'], $documentArrayData);
                        $resultArray = array(
                            'state' => 'success',
                            'message' => 'Successfully uploaded Document!',
                            'documentID' => $documentID 
                        );
					} else {
                        $resultArray = $rstDocument;
                    }
				} else {
                    $resultArray = array(
                        'state' => 'danger',
                        'message' => 'Error uploading Document!',
                        'documentID' => $documentID 
                    );
				}
			} else {
                $resultArray = array(
                    'state' => 'danger',
                    'message' => 'Invalid File Type: '.strtolower($extension),
                    'documentID' => $documentID 
                );
			}
		}
        echo json_encode(multiArrayMap('stripslashes',$resultArray), JSON_UNESCAPED_UNICODE);
	} elseif($action == 'refreshUploadedFiles') {
		if($relationID > 0) {
			$documentList = $oDocument->getAll( "AND d.relTypeID = ".$relTypeID." AND d.relationID = ".$relationID );
		} else {
			$documentList = $oDocument->getAll( "AND d.relTypeID = ".$relTypeID );
		}
		
		$oDocCat = new DocumentCategory();
		$docCatList = $oDocCat->getAll( "AND relTypeID = ".$relTypeID );
		
		$docListHTML = '
		<table id="documentLineDetails" class="table table-condensed table-striped table-bordered no-footer" width="100%">
			<thead>
				<tr>
					<th>Title</th>
					<th>Category</th>';
					if(in_array($relTypeID, array(14,15,16,19,20,21,22,38,39,41))) {
						$docListHTML .= '
						<th style="width:100px;">Review Date</th>';
					}
					$docListHTML .= '
					<th style="width:140px;">Uploaded Date</th>
					<th>Uploaded By</th>';
					if($relTypeID == 1 || $relTypeID == 2 || $relTypeID == 40) {
						$docListHTML .= '
						<th class="text-center" style="width:100px;">Inc. Van Pack</th>
						<th class="text-center" style="width:90px;">CAD Plan?</th>';
					}
					if($relTypeID == 10) {
						$docListHTML .= '
						<th class="text-center" style="width:120px;">Attach w/ Quote</th>';
					}
					$docListHTML .= '
					<th class="text-center" style="width:65px;">Confidential</th>
					<th class="text-center" style="width:80px;">Download</th>';
					if($relTypeID != 7){		
						$docListHTML .= '					 
						<th class="text-center" style="width:60px;">Tools</th>';
					}
					$docListHTML .= '
				</tr>
			</thead>
			<tbody>';
				if($documentList) {
					foreach($documentList as $dbDocument) {
						$baseFileName = preg_replace('/(.*)\.([^.]+)$/','\\1',$dbDocument['docFileName']);
						$extension = substr($dbDocument['docFileName'], strrpos($dbDocument['docFileName'], ".") + 1);
						
						$docImageSrc = '';
						if($extension == 'jpg' || $extension == 'jpeg') {
							$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/jpg.png" width="32" height="32" alt="JPG Document Icon"/>';
						} else if($extension == 'doc' || $extension == 'docx' || $extension == 'docm' || $extension == 'odt') {
							$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/doc.png" width="32" height="32" alt="DOC Document Icon"/>';
						} else if($extension == 'xls' || $extension == 'xlsx' || $extension == 'xlsm') {
							$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/xls.png" width="32" height="32" alt="XLS Document Icon"/>';
						} else if($extension == 'ppt' || $extension == 'pptx') {
							$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/ppt.png" width="32" height="32" alt="PPT Document Icon"/>';
						} else {
							$imgFilePath = ASSETS_IMG_ROOT.'icon/files/32x32/'.$extension.'.png';
							if(file_exists($imgFilePath)) {
								$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/'.$extension.'.png" width="32" height="32" alt="'.strtoupper($extension).' Document Icon" />';
							} else {
								$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/unknown.png" width="32" height="32" alt="Unknown Document Icon" />';
							}
						}
						$docListHTML .= '
						<tr id="'.$dbDocument['documentID'].'">
							<td>';
								if(isset($_POST['title_'.$dbDocument['documentID']])){$title = ($dbDocument['title'] != $_POST['title_'.$dbDocument['documentID']] ? $_POST['title_'.$dbDocument['documentID']] : $dbDocument['title']);}else{$title = $dbDocument['title'];}
								$docListHTML .= '
								<input type="text" class="form-control input-sm" id="title_'.$dbDocument['documentID'].'" name="title_'.$dbDocument['documentID'].'" value="'.$title.'">
							</td>
							<td>
								<select class="form-control input-sm" id="docCatID_'.$dbDocument['documentID'].'" name="docCatID_'.$dbDocument['documentID'].'">
									<option value="0">Category List</option>';
									if(isset($_POST['docCatID_'.$dbDocument['documentID']])){$docCatID = ($dbDocument['docCatID'] != $_POST['docCatID_'.$dbDocument['documentID']] ? $_POST['docCatID_'.$dbDocument['documentID']] : $dbDocument['docCatID']);}else{$docCatID = $dbDocument['docCatID'];}
									if($docCatList){
										foreach($docCatList as $dbDocCat){
											$checkSelectedResult = '';
											if( $docCatID == $dbDocCat['docCatID'] ){
												$checkSelectedResult = 'selected="selected"';
											}
											$docListHTML .= '
											<option value="'.$dbDocCat['docCatID'].'" '.$checkSelectedResult.'>'.$dbDocCat['name'].'</option>';
										}
									}		
								$docListHTML .= '
								</select>
							</td>';
							if(in_array($relTypeID, array(14,15,16,19,20,21,22,38,39,41,69))) {
								$docListHTML .= '
								<td class="text-center">';
									if(isset($_POST['expiryDate_'.$dbDocument['documentID']])){$expiryDate = ($dbDocument['expiryDate'] != $_POST['expiryDate'.$dbDocument['documentID']] ? $_POST['expiryDate_'.$dbDocument['documentID']] : $dbDocument['expiryDate']);}else{$expiryDate = $dbDocument['expiryDate'];}
									$docListHTML .= '
									<input type="text" class="form-control input-sm datepicker" id="expiryDate_'.$dbDocument['documentID'].'" name="expiryDate_'.$dbDocument['documentID'].'" value="'.(($expiryDate != NULL) ? $expiryDate : '').'" />
								</td>';
							}
							$docListHTML .= '
							<td>
								'.(($dbDocument['uploadedDate'] != '00/00/0000 00:00:00') ? $dbDocument['uploadedDate'] : '').'
							</td>
							<td>
								'.$dbDocument['uploadedByName'].'
							</td>';
							if($relTypeID == 1 || $relTypeID == 2 || $relTypeID == 40) {
								$docListHTML .= '
								<td class="text-center">';
									if(isset($_POST['includeVanPack_'.$dbDocument['documentID']])){$includeVanPack = ($dbDocument['includeVanPack'] != $_POST['includeVanPack_'.$dbDocument['documentID']] ? $_POST['includeVanPack_'.$dbDocument['documentID']] : $dbDocument['includeVanPack']);}else{$includeVanPack = $dbDocument['includeVanPack'];}
									$docListHTML .= '
									<input type="checkbox" id="includeVanPack_'.$dbDocument['documentID'].'" name="includeVanPack_'.$dbDocument['documentID'].'" '.(($includeVanPack == 1) ? 'checked="checked"' : '').' align="absmiddle" value="1" />
								</td>
								<td class="text-center">';
									if(isset($_POST['isCADPlan_'.$dbDocument['documentID']])){$isCADPlan = ($dbDocument['isCADPlan'] != $_POST['isCADPlan_'.$dbDocument['documentID']] ? $_POST['isCADPlan_'.$dbDocument['documentID']] : $dbDocument['isCADPlan']);}else{$isCADPlan = $dbDocument['isCADPlan'];}
									$docListHTML .= '<input type="checkbox" id="isCADPlan_'.$dbDocument['documentID'].'" name="isCADPlan_'.$dbDocument['documentID'].'" '.(($isCADPlan == 1) ? 'checked="checked"' : '').' align="absmiddle" value="1" />
								</td>';
							}
							if($relTypeID == 10) {
								$docListHTML .= '
								<td class="text-center">';
									if(isset($_POST['attachQuote_'.$dbDocument['documentID']])){$attachQuote = ($dbDocument['attachQuote'] != $_POST['attachQuote_'.$dbDocument['documentID']] ? $_POST['attachQuote_'.$dbDocument['documentID']] : $dbDocument['attachQuote']);}else{$attachQuote = $dbDocument['attachQuote'];}
									$docListHTML .= '
									<input type="checkbox" id="attachQuote_'.$dbDocument['documentID'].'" name="attachQuote_'.$dbDocument['documentID'].'" '.(($attachQuote == 1) ? 'checked="checked"' : '').' align="absmiddle" value="1" />
								</td>';
							}
							$docListHTML .= '
							<td class="text-center">
								<div class="checkbox">
									<label>';
										if(isset($_POST['isConfidential_'.$dbDocument['documentID']])){$isConfidential = ($dbDocument['isConfidential'] != $_POST['isConfidential_'.$dbDocument['documentID']] ? $_POST['isConfidential_'.$dbDocument['documentID']] : $dbDocument['isConfidential']);}else{$isConfidential = $dbDocument['isConfidential'];}
										$docListHTML .= '
										<input type="checkbox" style="margin-top:-8px;" id="isConfidential_'.$dbDocument['documentID'].'" name="isConfidential_'.$dbDocument['documentID'].'" value="1" '.(($isConfidential == 1) ? 'checked="checked"' : '').'>
									</label>
								</div>
							</td>
							<td class="text-center">
								<a href="'.ADMIN_URL.'documents/download.php?docid='.$dbDocument['documentID'].'" '.(($extension == 'pdf') ? 'target="_blank"' : '').'>'.$docImageSrc.'</a>
							</td>';
							if($relTypeID != 7) {
								$docListHTML .= '
								<td>
									<input type="hidden" class="displayOrder" id="displayOrder_'.$dbDocument['documentID'].'" name="displayOrder_'.$dbDocument['documentID'].'" value="'.$dbDocument['displayOrder'].'" />
									<i class="fal fa-arrows fa-lg"></i>
									<a href="javascript:void(0);" class="deleteDocument" data-toggle="tooltip" data-placement="left" data-original-title="Delete Document"><i class="fal fa-trash fa-lg"></i></a>
								</td>';
							}
							$docListHTML .= '
						</tr>';
					}
				} else {
					$docListHTML .= '
					<tr id="no-data">';
						if($relTypeID == 1 || $relTypeID == 2 || $relTypeID == 40) {
							$docListHTML .= '<td colspan="10">No Documents have been declared or added!</td>';
						} elseif($relTypeID == 10) {
							$docListHTML .= '<td colspan="9">No Documents have been declared or added!</td>';
						} elseif($relTypeID == 7) {
							$docListHTML .= '<td colspan="7">No Documents have been declared or added!</td>';
						} else {
							$docListHTML .= '<td colspan="8">No Documents have been declared or added!</td>';	
						}
						$docListHTML .= '
					</tr>';	
				}
			$docListHTML .= '
			</tbody>
		</table>';
		echo $docListHTML;
	} else if($action == 'saveFile') {
		$postArray = array();
		$postArray = urlDecodeToArray($postData);

		if($postArray) {
			$title = sqlEscape(strip_tags(trim($postArray['title'])));
			$expiryDate = $postArray['expiryDate'];
			$isConfidential = $postArray['isConfidential'];
			$localAuthAppTypeID = $postArray['localAuthAppTypeID'];
			$docCatID = $postArray['docCatID'];
			$includeVanPack = $postArray['includeVanPack'];
			$docAuthDistrictID = $postArray['docAuthDistrictID'];
			$docSuppSiteID = $postArray['docSuppSiteID'];
			$isCADPlan = $postArray['isCADPlan'];
		} else {
			$title = $docCatID = $docAuthDistrictID = $isConfidential = $isCADPlan = $includeVanPack = '';
		}
		if( $isConfidential == '' ){ $isConfidential = 0;}
		if( $includeVanPack == '' ){ $includeVanPack = 0;}
		if( $isCADPlan == '' ){ $isCADPlan = 0;}
		if( $localAuthAppTypeID == '' ){ $localAuthAppTypeID = 0;}
		if( $docAuthDistrictID == '' ){ $docAuthDistrictID = 0;}
		if( $docSuppSiteID == '' ){ $docSuppSiteID = 0;}
		
		if(!empty($expiryDate)) { $expiryDate = explode('/',$expiryDate); $mysql_expiryDate = $expiryDate[2].'-'.$expiryDate[1].'-'.$expiryDate[0]; } else { $mysql_expiryDate = NULL; }
		
		$updatedData = false;
		
		$documentData = array(
			'title' => $title,
			'docCatID' => $docCatID,
			'expiryDate' => $mysql_expiryDate,
			'includeVanPack' => $includeVanPack,
			'isCADPlan' => $isCADPlan,
			'localAuthAppTypeID' => $localAuthAppTypeID,
			'authDistrictID' => $docAuthDistrictID,
			'suppSiteID' => $docSuppSiteID,
			'isConfidential' => $isConfidential
		);
		
		$updateArray= array();
		$encryptArray = array(
			'title'
		);

		foreach ($documentData as $documentData => $value) {
			if (!in_array($documentData, $encryptArray) || $value == '') {
				if ($value == '') {
					$value = 'NULL';
				}
				if (!is_numeric($value)) {
					$value = "'".$value."'";
				}
			} else {
				$value = "AES_ENCRYPT('".$value."', '".ENCRYPTION_KEY."')";
			}
			$update = $documentData."=".$value;
			array_push($updateArray, $update);
		}
		$updateString = implode(", ", $updateArray);

		$sql = "UPDATE ".DB_NAME.'.'.DB_TBL_PREFIX."documents SET ".$updateString." WHERE documentID=".$docID;
		$sql = str_replace("'NULL'", 'NULL', $sql);

        $rstDocument = $oDocument->customSQL($sql);
		if($rstDocument['affectedRows'] > 0) {
			echo 'success|Document Information has been added';
		} else {
			echo 'info|No changes have been detected or amended';
		}
	} else if($action == 'massSaveFiles') {
		$postArray = array();
		$postArray = urlDecodeToArray($postData);

		if($postArray) {
			$docCatID = $postArray['massDocCatID'];
			$includeVanPack = $postArray['massIncludeVanPack'];
			$isCADPlan = $postArray['massIsCADPlan'];
		} else {
			$docCatID = $isCADPlan = $includeVanPack = '';
		}
		if( $includeVanPack == '' ){ $includeVanPack = 0;}
		if( $isCADPlan == '' ){ $isCADPlan = 0;}
		$updatedData = false;
		
		if($delRows) {
			foreach($delRows as $docID) {
				$documentData = array(
					'docCatID' => $docCatID,
					'includeVanPack' => $includeVanPack,
					'isCADPlan' => $isCADPlan
				);

				$rstDocument = $oDocument->update($documentData, $docID);
			}
		} else {
			echo 'info|Please select some documents before updating them';
		}
		if($rstDocument == true) {
			echo 'success|Document(s) Have been updated';
		} else {
			echo 'info|No changes have been detected or amended';
		}
	}elseif($action == 'saveOrdering') {
		if( $docID > 0 ) {
			$displayOrderData = array(
                'displayOrder' => $newDisplayOrder, 
            );
			$rstDocument = $oDocument->update($displayOrderData, $docID);
			if($rstDocument) {
				echo 'success|Document Information has been updated|'.$docID;
			} else {
				echo 'info|No changes have been detected or amended';
			}
		}
	} elseif($action == 'deleteFile') {
		$dbDocument = $oDocument->getOne($docID);
		$dbDocument = $dbDocument[0];

		$delResult = false;
		if (file_exists(UPLOADS_ROOT.$dbDocument['docFilePath'].$dbDocument['docFileName'])) {
			unlink(UPLOADS_ROOT.$dbDocument['docFilePath'].$dbDocument['docFileName']);
			$delResult = $oDocument->delete($docID);
		} else {
			$delResult = $oDocument->delete($docID);	
		}
		if($delResult == true) {
			$oActionLog->insert($_SESSION['fullname'].' has deleted document: '.$dbDocument['title'].' ('.$docID.') from the system', '');
		}
		echo $delResult;
	} elseif($action == 'deleteDocuments') {
		$delResult = false;
		if($delRows)
		{
			foreach($delRows as $documentID)
			{
				
				$dbDocument = $oDocument->getOne($documentID);
				$dbDocument = $dbDocument[0];

				$delResult = false;
				if (file_exists(UPLOADS_ROOT.$dbDocument['docFilePath'].$dbDocument['docFileName'])) {
					unlink(UPLOADS_ROOT.$dbDocument['docFilePath'].$dbDocument['docFileName']);
					$delResult = $oDocument->delete($documentID);
				} else {
					$delResult = $oDocument->delete($documentID);	
				}
				if($delResult == true) {
					$oActionLog->insert($_SESSION['fullname'].' has deleted document: '.$dbDocument['title'].' ('.$documentID.') from the system', '');
				}
				
			}
		}
		if ($delResult == true) {
			echo 'success|Document(s) have been removed.';
		}
	}
?>