<?php

	require_once('../../../../library/config.php');
	
	if ($oAuth->checkLoginStatus() == true)
	{
		if($oAuth->checkPermissionAccess('adminCompanyDocuments_All', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_PoliciesProcedures', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_RiskAssessments', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_MethodStatements', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_ToolboxTalks', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_Compliments', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_COSHHAssessments', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_EquipmentRegister', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_QualityManagementSystems', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_AttendanceDocumentSignatures', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_OperationalBibliography', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_Certification', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_MeetingMinutes', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_Insurances', 'edit') == true || $oAuth->checkPermissionAccess('adminCompanyDocuments_StaffMemos', 'edit') == true ) {
			
			$oTemplate->setInclude(ASSETS_JS_URL.'plugin/uploadifive/jquery.uploadifive.min.js', 'jsInclude');

			$relTypeID = isset($_REQUEST['reltypeid']) ? $_REQUEST['reltypeid'] : NULL;

			$footerCode = '
			<script type="text/javascript">
				$(document).ready(function()
				{					
					$(".datepicker").datepicker({ format: "dd/mm/yyyy", autoclose: true, clearBtn: true, todayHighlight: true });
					$(".datepicker").mask("99/99/9999");

					$("#file-uploads").uploadifive({
						debug    			: true,
						auto      			: true,
						uploadScript      	: "'.AJAX_URL.'documents/ajax-document.php",
						method   			: "POST",
						formData 			: { "action" : "uploadFiles", "reltypeid" : '.$relTypeID.' },
						fileSizeLimit		: "20MB",
						fileType  			: "image/* | audio/wav | audio/mpeg | application/msword | application/vnd.openxmlformats-officedocument.wordprocessingml.document | application/vnd.ms-excel | application/vnd.openxmlformats-officedocument.spreadsheetml.sheet | application/pdf|application/vnd.msoutlook | application/octet-stream",
						buttonClass			: "",
						buttonText			: "Drop files to upload (Or Click)",
						width				: "100%",
						height				: "70px",
						queueID          	: "file-uploads-queue",
						removeCompleted 	: true,
						multi				: true,
						dnd					: true,
						onFallback			: function()
						{
							alert("HTML5 is not supported in this browser.");
						},
						onQueueComplete 	: function(queueData)
						{
							var dataString = { "action" : "refreshUploadedFiles", "reltypeid" : '.$relTypeID.' };
							$.ajax(
							{
								type: "POST",
								url: "'.AJAX_URL.'documents/ajax-document.php",
								data: dataString,
								cache: false,
								success: function(result)
								{
									$("#docListUploadResults").html(result);								
									$("#documentLineDetails tbody").on("click", "a.deleteDocument", function () {
										var documentID = $(this).parent("td").parent("tr").attr("id");
										$( "#dlgDeleteDocument" ).modal( "show" );
										$( "#dlgDeleteDocument button#btnYes" ).unbind().on( "click" , function(e) {
											$.ajax({
												type: "POST",
												url: "'.AJAX_URL.'documents/ajax-document.php",
												data: { "action" : "deleteFile", "docid" : documentID },
												cache: false,
												success: function(data){
													if(data == true){
														$( "table#documentLineDetails tr#" + documentID ).remove();
														$( "#dlgDeleteDocument" ).modal( "hide" );
													}
												}
											});
										});
										$("#dlgDeleteDocument button#btnNo").unbind().on( "click" , function(e) {
											$("#dlgDeleteDocument").modal( "hide" );
										});
									});
								}
							});
						}
					});
					$("#documentLineDetails tbody").on("click", "a.deleteDocument", function () {
						var documentID = $(this).parent("td").parent("tr").attr("id");
						$( "#dlgDeleteDocument" ).modal( "show" );
						$( "#dlgDeleteDocument button#btnYes" ).unbind().on( "click" , function(e) {
							$.ajax({
								type: "POST",
								url: "'.AJAX_URL.'documents/ajax-document.php",
								data: { "action" : "deleteFile", "docid" : documentID },
								cache: false,
								success: function(data){
									if(data == true){
										$( "table#documentLineDetails tr#" + documentID ).remove();
										$( "#dlgDeleteDocument" ).modal( "hide" );
									}
								}
							});
						});
						$("#dlgDeleteDocument button#btnNo").unbind().on( "click" , function(e) {
							$("#dlgDeleteDocument").modal( "hide" );
						});
					});
				});
			</script>';
			$oTemplate->setInclude($footerCode,'footerCode');

			$oTemplate->setData('relTypeID', $relTypeID);

			$oRelType = new RelType();
			$dbRelType = $oRelType->getOne($relTypeID);
			$dbRelType = $dbRelType[0];
			$oTemplate->setData('relTypeName', $dbRelType['name']);

			$oDocument = new Document();
			$documentList = $oDocument->getAll( "AND d.relTypeID = ".$relTypeID );
			$oTemplate->setData('documentList', $documentList);

			$oDocCat = new DocumentCategory();
			$docCatList = $oDocCat->getAll( "AND relTypeID = ".$relTypeID );
			$oTemplate->setData('docCatList', $docCatList);

			if (request_is_post() && request_is_same_domain()) {
				if (!csrf_token_is_valid() || !csrf_token_is_recent()) {
					$oTemplate->setAlert('Invalid CSRF Security Token Received!', 'danger');
					$oTemplate->load(ADMIN_VIEWS.'company/documents/v_edit.php');
				} else {
					$documentList = $oDocument->getAll( "AND d.relTypeID = ".$relTypeID );
					if($documentList)
					{
						foreach($documentList as $dbDocument)
						{				
							if(!(isset($_POST['expiryDate_'.$dbDocument['documentID']]))) {$_POST['expiryDate_'.$dbDocument['documentID']] = '';}

							$errorMsg = array();
							if($_POST['title_'.$dbDocument['documentID']] == '')
							{ 
								$errorMsg[] = 'Title';
							}

							$countDocumentErr = 0;
							if(!empty($errorMsg))
							{
								$countDocumentErr = count($errorMsg);
								$errorMsg = implode(", ",$errorMsg);

								$oTemplate->setAlert('You must input '.$errorMsg.' as it is a required field within the Document Uploads Information Section', 'warning');
							}
						}
					}

					if($countDocumentErr > 0)
					{						
						$oTemplate->load(ADMIN_VIEWS.'company/documents/v_edit.php');
					}
					else
					{	
						if($documentList)
						{
							foreach($documentList as $dbDocument)
							{
								$expiryDate = $_POST['expiryDate_'.$dbDocument['documentID']];
								if(!empty($expiryDate)) { $expiryDate = explode('/',$_POST['expiryDate_'.$dbDocument['documentID']]); $mysql_expiryDate = $expiryDate[2].'-'.$expiryDate[1].'-'.$expiryDate[0]; } else { $mysql_expiryDate = NULL; }

								$documentData = array(
									'title' => $_POST['title_'.$dbDocument['documentID']],
									'docCatID' => $_POST['docCatID_'.$dbDocument['documentID']],
									'expiryDate' => $mysql_expiryDate,
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
								$sql = "UPDATE ".DB_NAME.'.'.DB_TBL_PREFIX."documents SET ".$updateString." WHERE documentID=".$dbDocument['documentID'];
								$sql = str_replace("'NULL'", 'NULL', $sql);

								$rstDocument = $oDocument->customSQL($sql);
								if($rstDocument['affectedRows'] > 0){
									$updatedData = true;	
									$oActionLog->insert($_SESSION['fullname'].' has updated '.$_POST['title_'.$dbDocument['documentID']].' document ('.$dbDocument['documentID'].') relating to '.$dbRelType['name'], $updateString);
								}
							}
						}

						if($updatedData == true) {					
							$oTemplate->setAlert('Company document details have been updated successfully', 'success');
						} else {
							$oTemplate->setAlert('No changes have been detected or amended','info');
						}

						if(isset($_POST['saveDocReturn'])) {
							$oTemplate->redirect(ADMIN_URL.'company/documents/index.php');
						} elseif(isset($_POST['saveDocStay'])) {
							$oTemplate->redirect(ADMIN_URL.'company/documents/edit.php?reltypeid='.$relTypeID);
						}
					}
				}
			} 
			else 
			{
				$oTemplate->load(ADMIN_VIEWS.'company/documents/v_edit.php');
			}
		} else {
			$oTemplate->setMetaData('Permission Denied!','pageTitle');
			$oTemplate->load(ADMIN_VIEWS.'v_403.php');
			exit();
		}
	}
	
?>