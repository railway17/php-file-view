<?php

    require_once('library/config.php');

    $oDocument = new Document();
    $oUser = new User();
    $oGroup = new PermissionGroup();
    $oRevision = new Revision();
    $oAccessInfo = new FolderAccessInfo();

    if(isset($_REQUEST['viewType'])) {
        $viewType = $_REQUEST['viewType'];
    }

    if(isset($_REQUEST['documentId'])) {
        $curDocId = $_REQUEST['documentId'];
        $curDoc = $oDocument->getOne($curDocId);
        $revisions = $oRevision->getByDocId($curDocId);
    }

    $accessInfo = $oAccessInfo->getByDocId($curDocId, $isFolder=0);
    $all_users = $oUser->getAll();
    $all_groups = $oGroup->getAll(); 

    $ext = end((explode(".", $curDoc['docFileName'])));
    $root = DIR_ROOT.DOCUMENTS_ROOT;
    $realPath = substr($curDoc['docFilePath'], strlen($root));
    
    $data['curDoc'] = $curDoc;
    $data['curDocId'] = $curDocId;
    $data['realPath'] = SITE_URL.DOCUMENTS_ROOT.$realPath;
    $data['viewType'] = $viewType;
    $data['users'] = $all_users;
    $data['groups'] = $all_groups;
    $data['revisions'] = $revisions;
    $data['accessinfo'] = $accessInfo;
    $data['accessModes'] = ['No Access', 'Read Permissions', 'Read-Write Permissions', 'All Permissions'];
    $oTemplate->setData('data', $data);
    $oTemplate->load('views/documents/index.php')
?>