<?php

	class FolderAccessInfo
	{
		function __construct()
		{
			global $oDatabase;
			$this->DB = $oDatabase;
		}

		function getAll($extraSQL = '', $orderBy = '')
		{
			$sql = 
			"
				SELECT			*
                FROM			".DB_TBL_PREFIX."folder_access_info
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}

		function getOne($infoId)
		{
			$sql = 
			"
				SELECT			*
				FROM			".DB_TBL_PREFIX."folder_access_info
                WHERE			id=$infoId
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0];
			} else {
				return false;
			}
        }
        
        function getByDocId($docId, $isFolder=1)
		{
			$sql = 
			"
				SELECT			A.*, CASE WHEN A.`groupId` IS NULL THEN CONCAT(U.`forename`, ' ', U.`surname`) ELSE B.`name` END AS `name` 
                FROM			".DB_TBL_PREFIX."folder_access_info A
                LEFT JOIN      ".DB_TBL_PREFIX."permissiongroups B on A.groupId=B.groupID
                LEFT JOIN      ".DB_TBL_PREFIX."users U on A.userId=U.userID
                WHERE			A.docId=$docId AND A.isDeleted=0 AND A.isFolder=$isFolder
            ";
            
            $stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return [];
			}
		}

        function getByFolderAndUser($docId, $userId, $isFolder=1)
		{
			$sql = 
			"
				SELECT			*
				FROM			".DB_TBL_PREFIX."folder_access_info
				WHERE			docId=$docId AND userId=$userId AND isFolder=$isFolder
				ORDER BY		createdAt DESC LIMIT 1
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0];
			} else {
				return false;
			}
        }

        function getByFolderAndGroup($docId, $groupId)
		{
			$sql = 
			"
				SELECT			*
				FROM			".DB_TBL_PREFIX."folder_access_info
                WHERE			docId=$docId AND groupId=$groupId
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0];
			} else {
				return false;
			}
        }
        
        function insert($data, $return_id = false)
        {
            $id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'folder_access_info', $data);
            if($id > 0) {
                if($return_id == true) {
                    return $id;
                } else {
                    return true;
                }
            } else {
                return $this->DB->getLastError();
            }
        }

        function update($data, $id)
        {
            $this->DB->where('id', $id);
            if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'folder_access_info', $data)) {
                if($this->DB->count > 0) {
					return true;
				} else {
					return false;
				}
            } else {
                return $this->DB->getLastError();
            }
		}
		
		function delete($id)
        {
            $data = array('isDeleted' => 1);
            $this->DB->where('docId', $id);
            if ($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'folder_access_info', $data)) {
                return true;
            } else {
                return false;
            }
        }
 	}

?>