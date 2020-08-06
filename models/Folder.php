<?php

	class Folder
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
				SELECT			A.*, CONCAT(B.forename, ' ', B.surname) AS OwnerName
                FROM			".DB_TBL_PREFIX."doc_folders A
                INNER JOIN      ".DB_TBL_PREFIX."users B ON A.ownerId = B.userID
                WHERE			A.isDeleted=0 
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}

		function getOne($folderId)
		{
			$sql = 
			"
				SELECT			*
				FROM			".DB_TBL_PREFIX."doc_folders
                WHERE			folderId=$folderId
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0];
			} else {
				return false;
			}
		}

        function getByPath($path)
		{
			$sql = 
			"
				SELECT			*
				FROM			".DB_TBL_PREFIX."doc_folders
                WHERE			folderPath='$path'
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0];
			} else {
				return false;
			}
        }
        
        function insert($data, $return_id = false, $setting=[])
        {
            if(!empty($setting)) {
                $data = array_merge($data, $setting);
            }
            $id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'doc_folders', $data);
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
            $this->DB->where('folderId', $id);
            if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'doc_folders', $data)) {
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
            $this->DB->where('folderId', $id);
            if ($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'doc_folders', $data)) {
                return true;
            } else {
                return false;
            }
        }
	}

?>