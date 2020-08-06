<?php

	class Revision
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
                FROM			".DB_TBL_PREFIX."revisions
                WHERE			isDeleted=0 
                GROUP BY        docId
                ORDER BY        docId ASC, revisedAt DESC
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}

		function getOne($revisionId)
		{
			$sql = 
			"
				SELECT			*
				FROM			".DB_TBL_PREFIX."revisions
                WHERE			revisionId=$revisionId
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0];
			} else {
				return false;
			}
        }
        
        function getByDocId($docId)
		{
			$sql = 
			"
				SELECT			*, CONCAT(U.`forename`, ' ', U.`surname`) as revisedUser
                FROM			".DB_TBL_PREFIX."revisions AS A
                INNER JOIN      ".DB_TBL_PREFIX."users AS U ON U.userID=A.revisedBy
                WHERE			1=1 AND A.docId=$docId AND A.isDeleted=0
                ORDER BY        A.revisedAt DESC
            ";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return [];
			}
		}

        function insert($data, $return_id = false)
        {
            $id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'revisions', $data);
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
            $this->DB->where('revisionId', $id);
            if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'revisions', $data)) {
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
            $this->DB->where('revisionId', $id);
            if ($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'revisions', $data)) {
                return true;
            } else {
                return false;
            }
        }
	}

?>