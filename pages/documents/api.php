<?php
    require_once('../../library/config.php');
    
    $_SESSION['userID'] = 126;


    switch($_REQUEST['endpoint']) {
        case 'getDocumentsInfo':
            getDocumentsInfo();
            break;
        case 'addNewSub':
            addNewSub($_POST);
            break;
        case 'deleteFolder':
            deleteFolder($_POST);
            break;
        case 'editFolder':
            editFolder($_POST);
            break;
        case 'editFolderOwner':
            editFolderOwner($_POST);
            break;
        case 'editFolderDefaultAccess':
            editFolderDefaultAccess($_POST);
            break;
        case 'editFolderAccess':
            editFolderAccess($_POST);
            break;
        case 'changeFolderAccess':
            changeFolderAccess($_POST);
            break;    
        case 'deleteFolderAccess':
            deleteFolderAccess($_POST);
            break;
        case 'upload':
            fileUpload($_POST, $_REQUEST['folderId']);
            break;
        case 'updateDocument':
            updateDocument($_POST, $_REQUEST['documentId']);
            break;
        case 'documentRename':
            documentRename($_POST);
            break;   
        case 'moveDocument':
            moveDocument($_POST);
            break;            
        case 'removeDocument':
            removeDocument($_POST);
            break;
        case 'editDocOwner':
            editDocOwner($_POST);
            break;
        case 'editDocDefaultAccess':
            editDocDefaultAccess($_POST);
            break;
        case 'editDocumentAccess':
            editDocumentAccess($_POST);
            break;
        case 'changeDocumentAccess':
            changeDocumentAccess($_POST);
            break;
        case 'getAccessMode':
            getAccessMode($_POST);
            break;
        default:
            break;
    }
    
    function getDocumentsInfo() {
        // $result = getDirContents(DIR_ROOT.DOCUMENTS_ROOT);
        $oFolder = new Folder();
        $oDocument = new Document();
        $folders = $oFolder->getAll();
        $docs = $oDocument->getAll();
        $result['folders'] = $folders;
        $result['docs'] = $docs;
        echo json_encode($result);
    }

    function addNewSub($data) {
        $folderCreateSetting = array(
            'defaultAccessMode' => ACCESS_MODE_All_PERMISSION,
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s'),
            'isDeleted' => 0
        );

        $oFolder = new Folder();
        $parentFolder = $oFolder->getOne($data['parentId']);
        $created = createNewFolder($parentFolder['folderPath'], $data['folderName']);
        $newFolderPath = ($parentFolder['folderPath'] == '' ? '/' : $parentFolder['folderPath']).$data['folderName'].'/';
        $newId = -1;
        if($created) {
            $subDoc = array(
                'folderName' => $data['folderName'],
                'parentId' =>  $parentFolder['folderId'],
                'folderPath' => $newFolderPath,
                'ownerId' => $_SESSION['userID']
            );

            $newId = $oFolder->insert($subDoc, true, $folderCreateSetting);
        }
        echo $newId;
    }

    function deleteFolder($data) {
        $oFolder = new Folder();
        $folder = $oFolder->getOne($data['folderId']);
        $path = makeFullFilePath($folder['folderPath']);
        $result = rrmdir($path);
        echo $result;
    }

    function rrmdir($dir, &$result=true) {
        $oFolder = new Folder();
        $oDocument = new Document();
        if (is_dir($dir)) { 
            $objects = scandir($dir);
            foreach ($objects as $object) { 
              if ($object != "." && $object != "..") { 
                if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object)) {
                    $deleted = rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                    if(!$deleted) {
                        $result = $result && false;
                    }
                } else {
                    $filePath = $dir. DIRECTORY_SEPARATOR .$object;
                    $deleted = unlink($dir. DIRECTORY_SEPARATOR .$object); 
                    if(!$deleted) {
                        $result = $result && false;
                    }
                    $doc = $oDocument->getByPath($filePath);
                    if($doc) {
                        $deleted = $oDocument->delete($doc['documentID']);
                    } else {
                        $result = $result && false;
                    }
                }
              } 
            }
            $deleted = rmdir($dir); 
            if($deleted) {
                $subPath = substr($dir, strlen(DIR_ROOT.DOCUMENTS_ROOT));
                $folder = $oFolder->getByPath($subPath);
                if($folder) {
                    $deleted = $oFolder->delete($folder['folderId']);
                } else {
                    $result = $result && false;
                }
            } else {
                $result = $result && false;
            }
        } 
        return $result;
    }

    function editFolder($data) {
        $oFolder = new Folder();
        $folder = $oFolder->getOne($data['folderId']);
        $renamed = renameFolder($folder['folderPath'], $data['folderName']);
        if($renamed) {
            $updateFolder = array (
                'folderName'=>$data['folderName'],
                'folderPath'=>$renamed,
                'updatedAt'=>date('Y-m-d H:i:s')
            );
            $res = $oFolder->update($updateFolder, $folder['folderId']);
            if($res) {
                echo $folder['folderId'];
                return;
            }
        }
        echo 0;
    }

    function editFolderOwner($data) {
        $oFolder = new Folder();
        $save = array(
            'ownerId'=>$data['userId']
        );
        $updated = $oFolder->update($save, $data['folderId']);
        echo $updated;
    }

    function editFolderDefaultAccess($data) {
        $oFolder = new Folder();
        $save = array(
            'defaultAccessMode'=>$data['defaultAccessMode']
        );
        $updated = $oFolder->update($save, $data['folderId']);
        echo $updated;
    }

    function editFolderAccess($data) {
        $acceessInfo = new FolderAccessInfo();
        $updateData = array(
                        'accessMode'=>$data['accessMode'],
                        'isDeleted'=>0
                    );
    
        $res_user = 1;
        $res_group = 1;
        if(!empty($data['userId'])) {
            $userinfo = $acceessInfo->getByFolderAndUser($data['folderId'], $data['userId']);

            if($userinfo) {
                $res_user = $acceessInfo->update($updateData, $userinfo['id']);
            } else {
                $insertData = array(
                    'docId'=>$data['folderId'],
                    'isFolder'=>1,
                    'accessMode'=>$data['accessMode'],
                    'userId'=>$data['userId'],
                    'createdAt'=>date('Y-m-d H:i:s'),
                    'updatedAt'=>date('Y-m-d H:i:s')
                );
                $res_user = $acceessInfo->insert($insertData, true);
            }
        }

        if(!empty($data['groupId'])) {
            $groupinfo = $acceessInfo->getByFolderAndGroup($data['folderId'], $data['groupId']);
            if($groupinfo) {
                $res_group = $acceessInfo->update($updateData, $groupinfo['id']);
            } else {
                $insertData = array(
                    'docId'=>$data['folderId'],
                    'isFolder'=>1,
                    'accessMode'=>$data['accessMode'],
                    'groupId'=>$data['groupId'],
                    'createdAt'=>date('Y-m-d H:i:s'),
                    'updatedAt'=>date('Y-m-d H:i:s')
                );
                $res_group = $acceessInfo->insert($insertData, true);
            }
        }
        
        echo $res_user && $res_group;
    }

    function changeFolderAccess($data) {
        $acceessInfo = new FolderAccessInfo();
        $save = array(
            'accessMode'=>$data['accessMode'],
            'updatedAt'=>date('Y-m-d H:i:s')
        );
        
        $res = $acceessInfo->update($save, $data['id']);
        echo $res;
    }

    function deleteFolderAccess($data) {
        $acceessInfo = new FolderAccessInfo();
        $save = array(
            'isDeleted'=>1,
            'updatedAt'=>date('Y-m-d H:i:s')
        );
        
        $res = $acceessInfo->update($save, $data['id']);
        echo $res;
    }
    
    function fileUpload($data, $folderId) {
        if ($_FILES['file']['error'] > 0) {
            echo false;
        }
        else {
            $oFolder = new Folder();
            $folder = $oFolder->getOne($folderId);
            $path = $folder['folderPath'] == '' ? '/' : $folder['folderPath'];
            $name = $_FILES["file"]["name"];
            $ext = end((explode(".", $name)));
            $uploadName = time().'.'.$ext;
            $targetPath = DIR_ROOT.DOCUMENTS_ROOT.$path.$uploadName;
            $res = move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
            if($res) {
                $oDocument = new Document();
                $oRevision = new Revision();
                $fileSize = FileSizeConvert(filesize($targetPath));
                $newDoc = array(
                    'folderId'=>$folder['folderId'],
                    'docFilePath'=>$targetPath,
                    'docFileName'=>$name,
                    'uploadedBy'=>$_SESSION['userID'],
                    'ownerId'=>$_SESSION['userID'],
                    'defaultAccessMode'=> ACCESS_MODE_All_PERMISSION,
                    'fileSize'=>$fileSize,
                    'uploadedDate'=>date('Y-m-d H:i:s'),
                );
                $res = $oDocument->insert($newDoc, true);

                if($res) {
                    addRevision($oRevision, $res, REVISION_ACTION['CREATED']);
                }
            }
            echo $res;
        }
    }

    function updateDocument($data, $documentId) {
        if ($_FILES['file']['error'] > 0) {
            echo false;
        }
        else {
            $oDocument = new Document();
            $oRevision = new Revision();
            $doc = $oDocument->getOne($documentId);
            unlink($doc['docFilePath']);
            $name = $_FILES["file"]["name"];
            $ext = end((explode(".", $name)));
            $uploadName = time().'.'.$ext;
            $lastIndex = strrpos($doc['docFilePath'], '/');
            $pre = substr($doc['docFilePath'], 0, $lastIndex);
            $targetPath = $pre.'/'.$uploadName;
            $res = move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);
            if($res) {
                $fileSize = FileSizeConvert(filesize($targetPath));
                $updateDoc = array(
                    'docFilePath'=>$targetPath,
                    'docFileName'=>$name,
                    'uploadedBy'=>$_SESSION['userID'],
                    'fileSize'=>$fileSize,
                    'uploadedDate'=>date('Y-m-d H:i:s'),
                );
                $res = $oDocument->update($updateDoc, $doc['documentID']);
                if($res) {
                    addRevision($oRevision, $doc['documentID'], REVISION_ACTION['UPDATED']);
                }
            }
            echo $res;
        }
    }

    function documentRename($data) {
        $oDocument = new Document();
        $oRevision = new Revision();
        $doc = $oDocument->getOne($data['documentId']);
        $oldName = $doc['docFileName'];
        if($data['keepExt']) {
            $ext = end((explode(".", $oldName)));
            $newName = $data['docName'].'.'.$ext;
        } else {
            $newName = $data['docName'];
        }

        $update = array(
            'docFileName'=>$newName
        );
        $res = $oDocument->update($update, $doc['documentID']);
        if($res) {
            addRevision($oRevision, $doc['documentID'], REVISION_ACTION['RENAMED']);
        }
        echo $res;
    }

    function moveDocument($data) {
        $oDocument = new Document();
        $oRevision = new Revision();
        $oFolder = new Folder();
        $doc = $oDocument->getOne($data['documentId']);
        $targetFolder = $oFolder->getOne($data['folderId']);
        $oldPath = $doc['docFilePath'];
        $targetPath = $targetFolder["folderPath"];
        if($data['targetPath'] == '') $targetPath = '/';
        $lastIndex = strrpos($doc['docFilePath'], '/');
        $realFile = substr($doc['docFilePath'], $lastIndex + 1, strlen($doc['docFilePath']) - 1);
        $targetPath = DIR_ROOT.DOCUMENTS_ROOT.$targetPath.$realFile;
        $update = array(
            'docFilePath'=>$targetPath,
            'folderId'=>$targetFolder['folderId'],
            'uploadedDate'=>date('Y-m-d H:i:s')
        );

        $res = $oDocument->update($update, $doc['documentID']);
        if($res) {
            addRevision($oRevision, $doc['documentID'], REVISION_ACTION['RENAMED']);
        }
        echo $res;
    }

    function removeDocument($data) {
        $oDocument = new Document();
        $doc = $oDocument->getOne($data['documentId']);
        if(!empty($doc)) {
            $res = $oDocument->delete($doc['documentID']);
        }
        echo $res;
    }

    function editDocOwner($data) {
        $oDocument = new Document();
        $oRevision = new Revision();
        $save = array(
            'ownerId'=>$data['userId']
        );
        $updated = $oDocument->update($save, $data['documentId']);
        if($updated) {
            addRevision($oRevision, $data['documentId'], REVISION_ACTION['REPERMITTED']);
        }
        echo $updated;
    }

    function editDocDefaultAccess($data) {
        $oDocument = new Document();
        $oRevision = new Revision();
        $save = array(
            'defaultAccessMode'=>$data['defaultAccessMode']
        );
        $updated = $oDocument->update($save, $data['documentId']);
        if($updated){
            addRevision($oRevision, $data['documentId'], REVISION_ACTION['REPERMITTED']);
        }
        echo $updated;
    }

    function editDocumentAccess($data) {
        $acceessInfo = new FolderAccessInfo();
        $oRevision = new Revision();
        $updateData = array(
                        'accessMode'=>$data['accessMode'],
                        'isDeleted'=>0
                    );
    
        $res_user = 1;
        $res_group = 1;
        if(!empty($data['userId'])) {
            $userinfo = $acceessInfo->getByFolderAndUser($data['documentId'], $data['userId'], 0);

            if($userinfo) {
                $res_user = $acceessInfo->update($updateData, $userinfo['id']);
            } else {
                $insertData = array(
                    'docId'=>$data['documentId'],
                    'isFolder'=>0,
                    'accessMode'=>$data['accessMode'],
                    'userId'=>$data['userId'],
                    'createdAt'=>date('Y-m-d H:i:s'),
                    'updatedAt'=>date('Y-m-d H:i:s')
                );
                $res_user = $acceessInfo->insert($insertData, true);
            }
        }

        if(!empty($data['groupId'])) {
            $groupinfo = $acceessInfo->getByFolderAndGroup($data['documentId'], $data['groupId']);
            if($groupinfo) {
                $res_group = $acceessInfo->update($updateData, $groupinfo['id']);
            } else {
                $insertData = array(
                    'docId'=>$data['documentId'],
                    'isFolder'=>0,
                    'accessMode'=>$data['accessMode'],
                    'groupId'=>$data['groupId'],
                    'createdAt'=>date('Y-m-d H:i:s'),
                    'updatedAt'=>date('Y-m-d H:i:s')
                );
                $res_group = $acceessInfo->insert($insertData, true);
            }
        }
        if($res_user && $res_group) {
            addRevision($oRevision, $data['documentId'], REVISION_ACTION['REPERMITTED']);
        }
        echo $res_user && $res_group;
    }

    function changeDocumentAccess($data){
        $acceessInfo = new FolderAccessInfo();
        $oRevision = new Revision();
        $save = array(
            'accessMode'=>$data['accessMode'],
            'updatedAt'=>date('Y-m-d H:i:s')
        );
        
        $res = $acceessInfo->update($save, $data['id']);
        if($res) {
            addRevision($oRevision, $data['documentId'], REVISION_ACTION['REPERMITTED']);
        }
        echo $res;
    }

    function getAccessMode($data) {
        $modes = ['No Access', 'Read Permissions', 'Read-Write Permissions', 'All Permissions'];
        $acceessInfo = new FolderAccessInfo();
        $info = $acceessInfo->getByFolderAndUser($data['docId'], $_SESSION['userID'], $data['isFolder']);
        if($info) {
            $acceessMode = $modes[$info['accessMode']];
            echo json_encode($acceessMode);
        }
    }

    function addRevision($oRevision, $docId, $action) {
        $revision = array(
            'docId'=>$docId,
            'revisedAction'=>$action,
            'revisedBy'=>$_SESSION['userID'],
            'revisedAt'=>date('Y-m-d H:i:s'),
        );
        return $oRevision->insert($revision);
    }

?>