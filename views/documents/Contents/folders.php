<div class='documents-contianer'>
    <div class='nav-container'>
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="<?php echo SITE_URL.'folders';?>">TMS</a>
                    <input type='hidden' name='cur_folder_id' id='cur_folder_id' value='<?php echo $this->getData('data')['curFolderId'];?>' />
                    <input type='hidden' name='view_type' id='view_type' value='FOLDER' />
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="folder_navigation">
                    <ul class="nav navbar-nav">
                        <li class='addFolder'><a>Add Subfolder</a></li>
                        <li class='addDoc'><a>Add Document</a></li>
                        <li class='editFolder'><a>Edit Folder</a></li>
                        <li class='editFolderAccess'><a>Edit Access</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
   
    <div class='content-wrapper'>
        <?php if($this->getData('data')['viewType'] == 'Dashboard') {
            include('folder_dashboard.php');
        } else if($this->getData('data')['viewType'] == 'AddFolder') {
            include('add_subfolder.php');
        } else if($this->getData('data')['viewType'] == 'Edit') {
            include('edit_folder.php');
        } else if($this->getData('data')['viewType'] == 'EditFolderAccess') {
            include('edit_folder_access.php');
        } else if($this->getData('data')['viewType'] == 'AddDoc') {
            include('add_doc.php');
        }?>
    </div>
</div>