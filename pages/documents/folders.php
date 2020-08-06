<?php
    require_once('library/config.php');

    $oFolder = new Folder();
    $oUser = new User();
    $oGroup = new PermissionGroup();
    $oAccessInfo = new FolderAccessInfo();

    $_SESSION['userID'] = 126;                        //test session id for login user
    $curFolderPath = '';
    $viewType = 'Dashboard';
    $curFolder = $oFolder->getByPath('');
    $curParentFolder = [];
    $folderCreateSetting = array(
        'defaultAccessMode' => ACCESS_MODE_All_PERMISSION,
        'createdAt' => date('Y-m-d H:i:s'),
        'updatedAt' => date('Y-m-d H:i:s'),
        'isDeleted' => 0
    );
    
    /**
     * check root directory
     */
    if(!checkDocumentRootExist()) {                 //if documents directory is not existing in uploads, will create new one
        mkdir(DIR_ROOT.DOCUMENTS_ROOT);
    };

    if(!$curFolder) {                  //check whether db has root directory row that folderPath='' and create new one if not exist
        $rootDoc = array(
            'folderName' => 'Documents',
            'parentId' => 0,
            'folderPath' => '',
            'ownerId' => $_SESSION['userID']
        );
        $newId = $oFolder->insert($rootDoc, true, $folderCreateSetting);
        $curFolder = array_merge($rootDoc, $folderCreateSetting);
        $curFolder['folderId'] = $newId;
    }
    
    $curFolderId = $curFolder['folderId'];

    /**
     * handle query param
     */
    if(isset($_REQUEST['viewType'])) {
        $viewType = $_REQUEST['viewType'];
    }

    if(isset($_REQUEST['folderId'])) {
        $curFolderId = $_REQUEST['folderId'];
        $curFolder = $oFolder->getOne($curFolderId);
    }
    
    if(isset($_REQUEST['parentId'])) {
        $parentFolderId = $_REQUEST['parentId'];
    }

    /**
     * handle post param
     */
    if(isset($_POST['parent_folder_id']) && !empty($_POST['parent_folder_id'])) {
        $pId = $_POST['parent_folder_id'];
        $curParentFolder = $oFolder->getOne($pId);
    }

    $all_users = $oUser->getAll();
    $all_groups = $oGroup->getAll(); 
    $accessInfo = $oAccessInfo->getByDocId($curFolderId);
    $data = [];
    $data['curFolder'] = $curFolder; //root directory
    $data['curFolderId'] = $curFolderId;
    $data['parentFolderId'] = $parentFolderId;
    $data['viewType'] = $viewType;
    $data['users'] = $all_users;
    $data['groups'] = $all_groups;
    $data['accessinfo'] = $accessInfo;
    $oTemplate->setData('data', $data);
    $oTemplate->load('views/documents/index.php');

?>