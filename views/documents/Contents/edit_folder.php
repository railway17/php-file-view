        <div class='row'>
            <div class='content'>
                <div class='row tree-view area'>
                    <div class='area-title'><h3>Edit Folder</h3></div>
                    <div class='info-area'>
                        <div class='row'>
                            <div class='col-md-3'>
                            <form id="editFolderForm" name="editFolderForm" class='needs-validation' role="form" data-toggle="validator" novalidate="true">
                                <div class="form schedule-assessment">
                                    <div class="row margin-top-l">
                                        <div class="form-group col-md-12">
                                            <label class="control-label" for="edit_folder_name">Folder Name:</label>
                                            <input name="edit_folder_name" id="edit_folder_name" type="text" class="form-control" required="required" data-error="Please enter folder name.">
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>  
                                <input type='hidden' name='manage_type' id='manage_type' value='EDIT' />
                                <input class="btn btn-primary" type="button" value="Edit" id="edit_folder"/>
                            </form>
                        <div>
                    </div>
                </div>
            </div> 
        </div>