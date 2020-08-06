        <div class='row'>
            <div class='col-md-4'>
                <div class='content'>
                    <div class='row tree-view area'>
                        <div class='area-title'><i class='fa fa-minus-circle tree-handler'></i></div>
                        <div id="tree"></div>
                    </div>
                    <div class='row clipboard area'>
                        <div class='area-title'><h3>Clipboard</h3></div>
                        <!-- <div class='clipboard-area'>Drag icon of folder or document here!</div> -->
                    </div>
                </div>
            </div>
            <div class='col-md-8'>
                <div class='content'>
                    <div class='row tree-view area'>
                        <div class='area-title'><h3>Folder Information</h3></div>
                        <div class='selected-info'>
                            <div class='row'><div class='col-md-3'>ID:</div><div class='col-md-9' id="folder_id">N/A</div></div>
                            <div class='row'><div class='col-md-3'>Owner:</div><div class='col-md-9' id="folder_owner">N/A</div></div>
                            <div class='row'><div class='col-md-3'>Created:</div><div class='col-md-9' id="folder_created">N/A</div></div>
                            <div class='row'><div class='col-md-3'>Default Access Mode:</div><div class='col-md-9' id="folder_dam">N/A</div></div>
                            <div class='row'><div class='col-md-3'>Access Mode:</div><div class='col-md-9' id="folder_am">N/A</div></div>
                        </div>
                    </div>
                    <div class='row tree-view area'>
                        <div class='area-title'><h3>Folder Contents</h3></div>
                        <div class='content-table'>
                        <table id="content_table" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
            <input type='hidden' id='cur_folder_path' value='<?php echo $this->getData('data')['curFolder']['folderPath']; ?>'>
        </div>