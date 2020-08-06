        <div class='row'>
            <div class='content'>
                <div class='area-title'><h3>Edit Access</h3></div>
                <div class='row tree-view area'>
                    <div class='col-md-4'>
                        <div class='info-area'>
                            <form id="setOwnerForm" name="setOwnerForm" class='needs-validation' role="form" data-toggle="validator" novalidate="true">
                                <div class="form ">
                                    <div class="row ">
                                        <div class="form-group col-md-12">
                                            <label class="control-label" for="owner_selector">Set Owner:</label>
                                            <select class="form-control" id="owner_selector">
                                                <?php $users = $this->getData('data')['users'];
                                                    $options = '';
                                                    foreach($users as $value) {
                                                        $selected = $this->getData('data')['curFolder']['ownerId'] == $value['userID'] ? 'selected' : '';
                                                        $options.='<option value='.$value['userID'].' '.$selected.'>'.$value['forename'].' '.$value['surname'].'</option>';
                                                    }
                                                    echo $options;
                                                ?>
                                            </select>
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                </div>  
                                <input type='hidden' name='manage_type' id='manage_type' value='EDIT_ACCESS' />
                                <input class="btn btn-primary" type="button" value="Save" id="btn_edit_folder_owner"/>
                            </form>
                        </div>
                    </div>

                    <div class='col-md-4'>
                        <div class='info-area'>
                            <form id="setDefaultAccessForm" name="setDefaultAccessForm" class='form-inline needs-validation' role="form" data-toggle="validator" novalidate="true">
                                <div class="form ">
                                    <div class="row ">
                                        <div class="form-group col-md-12">
                                                <label class="col-md-6 control-label mt-5" for="select_default_access">Default Access Mode:</label>
                                                <div class='col-md-6'>
                                                    <select class="form-control full-width-select" id="select_default_access">
                                                        <option value='0' <?php echo $this->getData('data')['curFolder']['defaultAccessMode'] == 0 ? 'selected' : ''; ?>>No Access</option>
                                                        <option value='1' <?php echo $this->getData('data')['curFolder']['defaultAccessMode'] == 1 ? 'selected' : ''; ?>>Read Permissions</option>
                                                        <option value='2' <?php echo $this->getData('data')['curFolder']['defaultAccessMode'] == 2 ? 'selected' : ''; ?>>Read-Write Permissions</option>
                                                        <option value='3' <?php echo $this->getData('data')['curFolder']['defaultAccessMode'] == 3 ? 'selected' : ''; ?>>All Permissions</option>
                                                    </select>
                                                </div>
                                            
                                            <div class='row'><input class="btn btn-primary col-md-offset-1" type="button" value="Save" id="btn_default_access_save"/></div>
                                        </div>
                                    </div>
                                </div>  
                                
                            </form>
                            <form id="setAccessForm" name="setAccessForm" class='form-inline needs-validation' role="form" data-toggle="validator" novalidate="true">
                                <div class="form mt-40">
                                    <div class="row mt-5">
                                        <div class="form-group col-md-12">
                                            <label class="col-md-6 control-label mt-5 text-right" for="select_access_user">User:</label>
                                            <div class='col-md-6'>
                                                <select class="form-control" id="select_access_user">
                                                    <?php $users = $this->getData('data')['users'];
                                                        $options = '<option value="0">Select User</option>';
                                                        foreach($users as $value) {
                                                            $options.='<option value='.$value['userID'].'>'.$value['forename'].' '.$value['surname'].'</option>';
                                                        }
                                                        echo $options;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="form-group col-md-12">
                                            <label class="col-md-6 control-label mt-5 text-right" for="select_access_group">Group:</label>
                                            <div class='col-md-6'>
                                                <select class="form-control" id="select_access_group">
                                                    <?php $users = $this->getData('data')['groups'];
                                                        $options = '<option value="0">Select Group</option>';
                                                        foreach($users as $value) {
                                                            $options.='<option value='.$value['groupID'].'>'.$value['name'].'</option>';
                                                        }
                                                        echo $options;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="form-group col-md-12">
                                            <label class="col-md-6 control-label mt-5 text-right" for="select_access_mode">Access Mode:</label>
                                            <div class='col-md-6'>
                                                <select class="form-control full-width-select" id="select_access_mode">
                                                    <option value='0'>No Access</option>
                                                    <option value='1'>Read Permissions</option>
                                                    <option value='2'>Read-Write Permissions</option>
                                                    <option value='3'>All Permissions</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='row mt-5'><input class="btn btn-primary col-md-offset-1" type="button" value="Add" id="btn_access_mode"/></div>
                                </div>  
                                
                            </form>
                        </div>
                    </div>
                    <div class='col-md-4'>
                        <?php $infos = $this->getData('data')['accessinfo'];
                            $list = '';
                            foreach($infos as $value) {
                                $list.='<div class="access-info-item row">
                                            <div class="col-md-3 mt-5">
                                                <i class="fa ';
                                                $icon = !$value['groupId'] ? 'fa-user' : 'fa-users';
                                                $list .= $icon.'"></i>
                                                <span>'.$value['name'].'</span>
                                            </div>
                                            <div class="col-md-5">
                                                <select class="form-control full-width-select access-info-item-select">
                                                    <option value="0" ';
                                                        $case1 = $value['accessMode']==0 ? 'selected' : '';
                                                        $list .= $case1.'>No Access</option>
                                                    <option value="1" ';
                                                        $case2 = $value['accessMode']==1 ? 'selected' : '';
                                                        $list.= $case2.'>Read Permissions</option>
                                                    <option value="2" ';
                                                        $case3 = $value['accessMode']==2 ? 'selected' : '';
                                                        $list.= $case3.'>Read-Write Permissions</option>
                                                    <option value="3" ';
                                                        $case4 = $value['accessMode']==3 ? 'selected' : '';
                                                        $list.= $case4.'>All Permissions</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <button class="access-item-save btn btn-primary" data-id="'.$value['id'].'">Save</button>
                                                <button class="access-item-delete btn btn-danger" data-id="'.$value['id'].'">Delete</button>
                                            </div>
                                        </div>';
                            }
                            echo $list;                        
                        ?>
                    </div>
                </div>
            </div> 
        </div>