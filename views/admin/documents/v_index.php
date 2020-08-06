<?php
    $oAuth = new Auth();
    if($_SESSION['hasMultiCompanyAccess'] == true) {
        $content = '
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-title txt-color-blueDark" style="margin:0 0 24px;">
                    <span class="fa-stack fa-2x" style="font-size:18px !important;padding-top:10px;">
                        <i class="fal fa-circle fa-stack-2x"></i>
                        <i class="fal fa-calendar-alt fa-stack-1x"></i> 
                    </span>
                    <strong>Companies:</strong> 
                    <select class="form-control input-sm" data-selected-text-format="count > 1" data-count-selected-text= "{0} Companies Selected" id="multiCompanyID" name="multiCompanyID[]" title="Company List" multiple>';	
                    if($this->getData('companyList')) {
                        foreach($this->getData('companyList') as $dbCompany) {
                            if($oAuth->checkPermissionAccess($this->getData('permissionName'), 'index', $dbCompany['companyID']) == true) {
                                $content .= '<option value="'.$dbCompany['companyID'].'" '.(($_SESSION['companyID'] == $dbCompany['companyID']) ? 'selected="selected" ' : '').'>'.$dbCompany['companyName'].'</option>';
                            }
                        }
                    }
                    $content .= '
                    </select> > <strong>'.$this->getData('relTypeName').' '.(($this->getData('relTypeID') == 30 || $this->getData('relTypeID') == 31 || $this->getData('relTypeID') == 32 || $this->getData('relTypeID') == 33 || $this->getData('relTypeID') == 34 || $this->getData('relTypeID') == 35) ? 'Audits' : 'Documents').'</strong> | <span><strong>Index Directory</strong></span>
                </h1>
            </div>
        </div>';
    } else { 
        $content = '
        <h1 class="page-title txt-color-blueDark" style="margin:0 0 24px;">
            <span class="fa-stack fa-2x" style="font-size:18px !important;padding-top:10px;">
                <i class="fal fa-circle fa-stack-2x"></i>
                <i class="fal fa-cabinet-filing fa-stack-1x"></i>
            </span> 
            <strong>'.$this->getData('relTypeName').' '.(($this->getData('relTypeID') == 30 || $this->getData('relTypeID') == 31 || $this->getData('relTypeID') == 32 || $this->getData('relTypeID') == 33 || $this->getData('relTypeID') == 34 || $this->getData('relTypeID') == 35) ? 'Audits' : 'Documents').'</strong> | <span><strong>Index Directory</strong></span>
        </h1>';
    }
    $content .= '
    <section id="widget-grid">';
        if(!isset($_GET['doctype'])) {
            $content .= '
            <div class="row">
                <article class="col-sm-12 col-md-12 col-lg-6">
                    <div class="jarviswidget jarviswidget-color-darken" data-widget-deletebutton="false" data-widget-editbutton="false">
                        <header>
                            <h2>Upload Files</h2>
                        </header>
                        <div>
                            <div class="widget-body">
                                <div class="form-group row">
                                    <label for="companyID" class="col-sm-2 control-label">Company:</label>
                                    <div class="col-sm-10 selectFeedback">
                                        <select class="form-control input-sm selectPicker" id="uploadCompanyID" name="uploadCompanyID" title="Company List">';	
                                        if($this->getData('companyList')) {
                                            foreach($this->getData('companyList') as $dbCompany) {
                                                if($oAuth->checkPermissionAccess($this->getData('permissionName'), 'add', $dbCompany['companyID']) == true) {
                                                    $content .= '<option value="'.$dbCompany['companyID'].'" '.(($_SESSION['companyID'] == $dbCompany['companyID']) ? 'selected="selected"' : '').'>'.$dbCompany['companyName'].' - '.$dbCompany['addressLine1'].', '.$dbCompany['addressLine2'].', '.$dbCompany['addressLine3'].', '.$dbCompany['addressLine4'].', '.$dbCompany['addressLine5'].'</option>';
                                                }
                                            }
                                        }
                                        $content .= '
                                        </select>
                                    </div>	
                                </div>
                                <div class="form-group row">
                                    <label for="docCatID" class="col-sm-2 control-label">Category:</label>
                                    <div class="col-sm-10">
                                        <select class="form-control input-sm" id="upoadDocCatID" name="upoadDocCatID">
                                            <option id="default" name="default" value="0">Category List</option>';
                                            if($this->getData('docCatList')){
                                                foreach($this->getData('docCatList') as $dbDocCat) {
                                                    $content .= '<option value="'.$dbDocCat['docCatID'].'">'.$dbDocCat['name'].'</option>';
                                                }
                                            }		
                                            $content .= '
                                        </select>
                                    </div>
                                </div>
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
                            </div>
                        </div>
                    </div>
                </article>
            </div>';
        }
        $content .= '
		<div class="row">
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<div class="jarviswidget" id="wid-id-3" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false">
					<ul class="nav nav-tabs" id="companyTabs" role="tablist">';
                    if($_SESSION['hasMultiCompanyAccess'] == true) {                    
                        if($this->getData('companyList')){
                            foreach($this->getData('companyList') as $dbCompany){
                                if($oAuth->checkPermissionAccess($this->getData('permissionName'), 'index', $dbCompany['companyID']) == true) {
                                    $content .= '
                                    <li id="company'.$dbCompany['companyID'].'Tab" '.(($dbCompany['companyID'] == $_SESSION['companyID']) ? ' style="display:block"' : 'style="display:none"' ).'><a href="#'.$dbCompany['companyID'].'" role="tab" data-toggle="tab">'.$dbCompany['companyName'].'</a></li>';
                                }
                            }
                        }
                    } else {
                        $content .= '
                        <li id="company'.$_SESSION['companyID'].'Tab" style="display:block;width:100%;">
                            <a class="tab-widget" href="#'.$_SESSION['companyID'].'" role="tab" data-toggle="tab">Document Information</a>
                        </li>';
                    }
                    $content .= '
                    </ul>
                    <div class="tab-content no-padding">';
                    if($this->getData('companyList')) {
                        foreach($this->getData('companyList') as $dbCompany) {
                            if($oAuth->checkPermissionAccess($this->getData('permissionName'), 'index', $dbCompany['companyID']) == true) {
                                $content .= '
                                <div class="tab-pane fade" id="'.$dbCompany['companyID'].'">
                                    <ul class="nav nav-tabs" id="documentMonthYearTab'.$dbCompany['companyID'].'" role="tablist">';
                                        if($this->getData('y2dMonthArray')) {
                                            foreach($this->getData('y2dMonthArray') as $date) {
                                                $content .= '
                                                <li><a href="#comp'.$dbCompany['companyID'].'_'.strtolower($date['yearTag']).'" role="tab" data-toggle="tab">'.$date['yearTitle'].'</a></li>';
                                            }
                                        }
                                        $content .= '
                                    </ul>
                                    <div class="tab-content no-padding">';
                                        if($this->getData('y2dMonthArray')) {
                                            foreach($this->getData('y2dMonthArray') as $date) {
                                                $content .= '
                                                <div class="tab-pane fade" id="comp'.$dbCompany['companyID'].'_'.strtolower($date['yearTag']).'">
                                                    <div class="dataTables_wrapper table-responsive form-inline no-footer">
                                                        <table id="comp'.$dbCompany['companyID'].'_'.strtolower($date['yearTag']).'documentListDT" class="table table-condensed table-striped table-bordered" width="100%" cellpadding="0" cellspacing="0" role="datatable">
                                                             <thead>
                                                                <tr class="hidden-xs">
                                                                    <th class="hasinput">
                                                                        <input type="text" id="dt_fltr_0" class="form-control input-sm" placeholder="Column Search ..." />
                                                                    </th>
                                                                    <th class="hasinput">
                                                                        <input type="text" id="dt_fltr_1" class="form-control input-sm" placeholder="Column Search ..." />
                                                                    </th>
                                                                    <th class="hasinput">
                                                                        <input type="text" id="dt_fltr_2" class="form-control input-sm" placeholder="Column Search ..." />
                                                                    </th>
                                                                    <th class="hasinput">
                                                                        <input type="text" id="dt_fltr_3" class="form-control input-sm" placeholder="Column Search ..." />
                                                                    </th>
                                                                    <th>
                                                                        <input type="text" id="dt_fltr_4" class="form-control input-sm " placeholder="Column Search ..." />
                                                                    </th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th></th>
                                                                </tr>
                                                                <tr>
                                                                    <th width="250px">Type</th>
                                                                    <th>Title</th>
                                                                    <th>Category</th>
                                                                    <th width="115px">'.(($this->getData('relTypeID') == 34) ? 'Audit Date' : 'Expiry Date').'</th>
                                                                    <th width="115px">Uploaded Date</th>
                                                                    <th width="115px">Uploaded Time</th>
                                                                    <th width="125px;">Uploaded By</th>
                                                                    <th width="65px">File Type</th>
                                                                    <th width="60px"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>';
                                            }
                                        }
                                        $content .= '  
                                    </div>
                                </div>';
                            }
                        }
                    }
                    $content .= '        
					</div>
				</div>
			</article>
		</div>
    </section>
    <div id="dlgDocumentDetails" class="modal fade" data-backdrop="static" data-keyboard="false" role="dialog">
        <div class="modal-dialog" style="width:40%;">
            <div class="modal-content">
                <!-- dialog Header -->
                <div class="modal-header">
                    <h4 class="modal-title txt-color-blueDark"><strong>Document Details</strong></h4>
                </div>
                <!-- dialog body -->
                <div class="modal-body">
                    <form id="updateDocument" name="updateDocument" class="form-horizontal" method="POST" action="" role="form">
                        <input type="hidden" class="form-control input-sm" id="documentID" name="documentID" value="" />
                        <div class="form-group row">
                            <label for="title" class="col-sm-2 control-label">Title:</label>
                            <div class="col-sm-10">
                                <input type="title" class="form-control input-sm" id="title" name="title" value="" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="docCatID" class="col-sm-2 control-label">Category:</label>
                            <div class="col-sm-10">
                                <select class="form-control input-sm" id="docCatID" name="docCatID">
                                    <option id="default" name="default" value="0">Category List</option>';
                                    if($this->getData('docCatList')){
                                        foreach($this->getData('docCatList') as $dbDocCat) {
                                            $content .= '<option value="'.$dbDocCat['docCatID'].'">'.$dbDocCat['name'].'</option>';
                                        }
                                    }		
                                    $content .= '
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="title" class="col-sm-2 control-label">'.(($this->getData('relTypeID') == 34) ? 'Audit Date' : 'Expiry Date').':</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control input-sm datepicker" id="expiryDate" name="expiryDate" value="" />
                            </div>
                        </div>
                        <div class="form-group row" style="margin-bottom:0px;">
                            <label for="uploadedDate" class="col-sm-2 control-label">Uploaded:</label>
                            <div class="col-sm-4">
                                <p id="uploadedDate" class="form-control-static"></p>
                            </div>
                            <label for="uploadedBy" class="col-sm-2 control-label">Uploaded By:</label>
                            <div class="col-sm-4">
                                <p id="uploadedBy" class="form-control-static"></p>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- dialog buttons -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger pull-left col-md-2" id="btnCancel">Cancel</button>
                    <button type="button" class="btn btn-primary pull-right col-md-2" id="btnSave">Save</button>
                </div>
            </div>
        </div>
    </div>
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
                    <button type="button" class="btn btn-danger pull-left col-md-2" id="btnNo">Cancel</button>
                    <button type="button" class="btn btn-primary pull-right col-md-2" id="btnYes">Delete</button>
                </div>
            </div>
        </div>
    </div>';

	include(ADMIN_VIEWS.'v_template.php');;

?>