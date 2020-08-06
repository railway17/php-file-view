<?php
	$oAuth = new Auth();
	$content = '
	<h1 class="page-title txt-color-blueDark"><i class="fal fa-lg fa-fw fa-files-o"></i> Company File: <strong>'.$this->getData('relTypeName').' Directory</strong></h1>
	<section id="widget-grid">
		<form id="editDocuments" name="editDocuments" class="form-horizontal" method="POST" action="" role="form">	
			'.csrf_token_tag().'
			<div class="row">
                <article class="col-sm-12 col-md-12 col-lg-6">
                    <div class="jarviswidget jarviswidget-color-darken" data-widget-deletebutton="false" data-widget-editbutton="false">
                        <header>
                            <h2>Upload Files</h2>
                        </header>
                        <div>
                            <div class="jarviswidget-editbox">
                            </div>
                            <div class="widget-body">
                                <fieldset>
                                    <div class="form-group row m-b-none">
                                        <label for="document-upload" class="col-sm-2 control-label">Files:</label>
                                        <div class="col-sm-10">
                                            <input type="file" class="form-control input-sm" name="file-uploads" id="file-uploads" />
                                        </div>
                                        <label for="document-upload" class="col-sm-2 control-label">Queue:</label>
                                        <div class="col-sm-10">
                                            <div id="file-uploads-queue"></div>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
            <div class="row">
                <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="jarviswidget jarviswidget-color-darken" id="wid-id-0" data-widget-deletebutton="false" data-widget-editbutton="false">
                        <header>
                            <h2>Documents Directory</h2>
                        </header>
                        <div>
                            <div class="widget-body no-padding">
                                <div class="dataTables_wrapper table-responsive form-inline no-footer">
									<div id="docListUploadResults">
										<table id="documentLineDetails" class="table table-condensed table-striped table-bordered no-footer" width="100%">
											<thead>
												<tr>
													<th>Title</th>
													<th>Category</th>';
													if(in_array($this->getData('relTypeID'), array(14,15,16,19,20,21,22,38,39,41))) 
													{
														$content .= '
														<th style="width:120px;">Review Date</th>';
													}
													$content .= '
													<th style="width:140px;">Uploaded Date</th>
													<th>Uploaded By</th>
													<th class="text-center" style="width:80px;">Download</th>
													<th class="text-center" style="width:60px;">Tools</th>
												</tr>
											</thead>
											<tbody>';
												if($this->getData('documentList'))
												{
													foreach($this->getData('documentList') as $dbDocument)
													{
														$baseFileName = preg_replace('/(.*)\.([^.]+)$/','\\1',$dbDocument['docFileName']);
														$extension = substr($dbDocument['docFileName'], strrpos($dbDocument['docFileName'], ".") + 1);

														$docImageSrc = '';
														if($extension == 'jpg' || $extension == 'jpeg')
														{
															$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/jpg.png" width="32" height="32" alt="JPG Document Icon"/>';
														} 
														else if($extension == 'doc' || $extension == 'docx')
														{
															$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/doc.png" width="32" height="32" alt="DOC Document Icon"/>';
														}
														else if($extension == 'xls' || $extension == 'xlsx')
														{
															$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/xls.png" width="32" height="32" alt="XLS Document Icon"/>';
														}
														else if($extension == 'ppt' || $extension == 'pptx')
														{
															$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/ppt.png" width="32" height="32" alt="PPT Document Icon"/>';
														}
														else
														{
															$imgFilePath = ASSETS_IMG_ROOT.'icons/files/32x32/'.$extension.'.png';
															if(file_exists($imgFilePath))
															{
																$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/'.$extension.'.png" width="32" height="32" alt="'.strtoupper($extension).' Document Icon" />';
															}
															else
															{
																$docImageSrc .= '<img src="'.ASSETS_ICON_URL.'files/32x32/unknown.png" width="32" height="32" alt="Unknown Document Icon" />';
															}
														}

														$content .= '
														<tr id="'.$dbDocument['documentID'].'">
															<td>';
																if(isset($_POST['title_'.$dbDocument['documentID']])){$title = ($dbDocument['title'] != $_POST['title_'.$dbDocument['documentID']] ? $_POST['title_'.$dbDocument['documentID']] : $dbDocument['title']);}else{$title = $dbDocument['title'];}
																$content .= '
																<input type="text" class="form-control input-sm" style="width:100%" id="title_'.$dbDocument['documentID'].'" name="title_'.$dbDocument['documentID'].'" value="'.$title.'">
															</td>
															<td>
																<select class="form-control input-sm" style="width:100%" id="docCatID_'.$dbDocument['documentID'].'" name="docCatID_'.$dbDocument['documentID'].'">
																	<option id="default" name="default" value="0">Category List</option>';
																	if(isset($_POST['docCatID_'.$dbDocument['documentID']])){$docCatID = ($dbDocument['docCatID'] != $_POST['docCatID_'.$dbDocument['documentID']] ? $_POST['docCatID_'.$dbDocument['documentID']] : $dbDocument['docCatID']);}else{$docCatID = $dbDocument['docCatID'];}
																	if($this->getData('docCatList'))
																	{
																		foreach($this->getData('docCatList') as $dbDocCat)
																		{
																			$checkSelectedResult = '';
																			if( $docCatID == $dbDocCat['docCatID'] )
																			{
																				$checkSelectedResult = 'selected="selected"';
																			}
																			$content .= '
																			<option value="'.$dbDocCat['docCatID'].'" '.$checkSelectedResult.'>'.$dbDocCat['name'].'</option>';
																		}
																	}		
																$content .= '
																</select>
															</td>';
															if(in_array($this->getData('relTypeID'), array(14,15,16,19,20,21,22,38,39,41))) 
															{
																$content .= '
																<td class="text-center">';
																if(isset($_POST['expiryDate_'.$dbDocument['documentID']])){$expiryDate = ($dbDocument['expiryDate'] != $_POST['expiryDate'.$dbDocument['documentID']] ? $_POST['expiryDate_'.$dbDocument['documentID']] : $dbDocument['expiryDate']);}else{$expiryDate = $dbDocument['expiryDate'];}
																$content .= '
																<input type="text" class="form-control input-sm datepicker" style="width:100%"  id="expiryDate_'.$dbDocument['documentID'].'" name="expiryDate_'.$dbDocument['documentID'].'" value="'.(($expiryDate != '00/00/0000') ? $expiryDate : '').'" />
																</td>';
															}
															$content .= '
															<td>'.(($dbDocument['uploadedDate'] != '00/00/0000 00:00:00') ? $dbDocument['uploadedDate'] : '').'</td>
															<td>'.$dbDocument['uploadedByName'].'</td>
															<td class="text-center">';
																if($dbDocument['relTypeID'] == 14) {
																	$permissionName = 'adminCompanyDocuments_PoliciesProcedures';
																} elseif($dbDocument['relTypeID'] == 15) {
																	$permissionName = 'adminCompanyDocuments_RiskAssessments';
																} elseif($dbDocument['relTypeID'] == 16) {
																	$permissionName = 'adminCompanyDocuments_MethodStatements';
																} elseif($dbDocument['relTypeID'] == 17) {
																	$permissionName = 'adminCompanyDocuments_ToolboxTalks';
																} elseif($dbDocument['relTypeID'] == 18) {
																	$permissionName = 'adminCompanyDocuments_Compliments';
																} elseif($dbDocument['relTypeID'] == 19) {
																	$permissionName = 'adminCompanyDocuments_COSHHAssessments';
																} elseif($dbDocument['relTypeID'] == 20) {
																	$permissionName = 'adminCompanyDocuments_EquipmentRegister';
																} elseif($dbDocument['relTypeID'] == 21) {
																	$permissionName = 'adminCompanyDocuments_QualityManagementSystems';
																} elseif($dbDocument['relTypeID'] == 36) {
																	$permissionName = 'adminCompanyDocuments_AttendanceDocumentSignatures';
																} elseif($dbDocument['relTypeID'] == 37) {
																	$permissionName = 'adminCompanyDocuments_MeetingMinutes';
																} elseif($dbDocument['relTypeID'] == 38) {
																	$permissionName = 'adminCompanyDocuments_OperationalBibliography';
																} elseif($dbDocument['relTypeID'] == 39) {
																	$permissionName = 'adminCompanyDocuments_Certification';
																} elseif($dbDocument['relTypeID'] == 22) {
																	$permissionName = 'adminCompanyDocuments_Insurances';
																} elseif($dbDocument['relTypeID'] == 41) {
																	$permissionName = 'adminCompanyDocuments_StaffMemos';
																}

																if($oAuth->checkPermissionAccess($permissionName, 'view') == true) {
																	$content .='<a href="'.ADMIN_URL.'documents/download.php?docid='.$dbDocument['documentID'].'" '.(($extension == 'pdf') ? 'target="_blank"' : '').'>'.$docImageSrc.'</a>';
																}
															$content .= '
															</td>
															<td>
																<input type="hidden" class="displayOrder" id="displayOrder_'.$dbDocument['documentID'].'" name="displayOrder_'.$dbDocument['documentID'].'" value="'.$dbDocument['displayOrder'].'" />
																<i class="fal fa-arrows fa-lg"></i>';
																if($dbDocument['relTypeID'] == 14) {
																	$permissionName = 'adminCompanyDocuments_PoliciesProcedures';
																} elseif($dbDocument['relTypeID'] == 15) {
																	$permissionName = 'adminCompanyDocuments_RiskAssessments';
																} elseif($dbDocument['relTypeID'] == 16) {
																	$permissionName = 'adminCompanyDocuments_MethodStatements';
																} elseif($dbDocument['relTypeID'] == 17) {
																	$permissionName = 'adminCompanyDocuments_ToolboxTalks';
																} elseif($dbDocument['relTypeID'] == 18) {
																	$permissionName = 'adminCompanyDocuments_Compliments';
																} elseif($dbDocument['relTypeID'] == 19) {
																	$permissionName = 'adminCompanyDocuments_COSHHAssessments';
																} elseif($dbDocument['relTypeID'] == 20) {
																	$permissionName = 'adminCompanyDocuments_EquipmentRegister';
																} elseif($dbDocument['relTypeID'] == 21) {
																	$permissionName = 'adminCompanyDocuments_QualityManagementSystems';
																} elseif($dbDocument['relTypeID'] == 36) {
																	$permissionName = 'adminCompanyDocuments_AttendanceDocumentSignatures';
																} elseif($dbDocument['relTypeID'] == 37) {
																	$permissionName = 'adminCompanyDocuments_MeetingMinutes';
																} elseif($dbDocument['relTypeID'] == 38) {
																	$permissionName = 'adminCompanyDocuments_OperationalBibliography';
																} elseif($dbDocument['relTypeID'] == 39) {
																	$permissionName = 'adminCompanyDocuments_Certification';
																} elseif($dbDocument['relTypeID'] == 22) {
																	$permissionName = 'adminCompanyDocuments_Insurances';
																} elseif($dbDocument['relTypeID'] == 41) {
																	$permissionName = 'adminCompanyDocuments_StaffMemos';
																}

																if($oAuth->checkPermissionAccess($permissionName, 'delete') == true) {
																	$content .='<a href="javascript:void(0);" class="deleteDocument" data-toggle="tooltip" data-placement="left" data-original-title="Delete Document"><i class="fal fa-trash fa-lg"></i></a>';
																}
																$content .='
															</td>
														</tr>';
													}
												} else {
													$content .= '
													<tr id="no-doc-data">
														<td colspan="7">No Documents have been declared or added!</td>
													</tr>';	
												}
											$content .= '
											</tbody>
										</table>
									</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
			<div class="btn-group">
				<input type="submit" class="btn btn-primary" id="saveDocStay" name="saveDocStay" value="Save & Stay" />
				<input type="submit" class="btn btn-info" id="saveDocReturn" name="saveDocReturn" value="Save & Return" />
			</div>
		</form>
        <div id="dlgDeleteDocument" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- dialog body -->
                    <div class="modal-header">
                        <h4 class="modal-title txt-color-blueDark"><strong>Delete Document Details</strong></h4>
                    </div>
                    <!-- dialog body -->
                    <div class="modal-body">
                        <p>Are you sure you want to delete this Document?</p>
                    </div>
                    <!-- dialog buttons -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger pull-left col-md-2" id="btnNo">No</button>
                        <button type="button" class="btn btn-primary pull-right col-md-2" id="btnYes">Yes</button>
                    </div>
                </div>
            </div>
        </div>
	</section>';
	
	include(ADMIN_VIEWS.'v_template.php');
	
?>