        <div class='row'>
            <div class='content'>
                <div class='row tree-view area'>
                    <div class='area-title'><h3>Folder Information</h3></div>
                    <div class='info-area'>
                        <div class='row'>
                            <div class='col-md-3'>
                            <form id="addSubFolderForm" name="addSubFolderForm" class='needs-validation' role="form" data-toggle="validator" novalidate="true">
                                <div class="form schedule-assessment">
                                    <div class="row margin-top-l">
                                        <div class="form-group col-md-12">
                                            <label class="control-label" for="sub_folder_name">Folder Name:</label>
                                            <input name="sub_folder_name" id="sub_folder_name" type="text" class="form-control" required="required" data-error="Please enter folder name.">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>  
                                <input type='hidden' name='parent_folder_id' id='parent_folder_id' value='<?php echo $this->getData('data')['parentFolderId'];?>' />
                                <input type='hidden' name='manage_type' id='manage_type' value='NEW' />
                                <input class="btn btn-primary" type="button" value="Add Subfolder" id="add_new_folder"/>
                            </form>
                        <div>
                    </div>
                </div>
            </div> 
        </div>