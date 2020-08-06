<?php

	class DocumentCategory
	{
		function __construct()
		{
			global $oDatabase;
			$this->DB = $oDatabase;
		}

		function getAll($extraSQL = '')
		{
			$sql = 
			"
				SELECT			docCatID, relTypeID, name, displayOrder, isDeleted
				FROM			".DB_TBL_PREFIX."documentcategories
				WHERE			1=1
				".$extraSQL."
				AND				isDeleted = 0
				ORDER BY 		displayOrder ASC
			";
			
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
	
		function getOne($docCatID)
		{
			$sql = 
			"
				SELECT			docCatID, relTypeID, name, displayOrder, isDeleted
				FROM			".DB_TBL_PREFIX."documentcategories
				WHERE			1=1
				AND				docCatID = ".$docCatID."
			";
			
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
		function customSQL($sql, $return_id = false, $bindParams = null){
			$result = $this->DB->rawReturnID($sql, $bindParams, $return_id);
            if($result['state'] != '' && $result['state'] != NULL){    
                return $result;
            } else {
                return $this->DB->getLastError();    
            }
		}
		
		function insert($data, $return_id = false)
		{
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'documentcategories', $data);
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
			$this->DB->where('docCatID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'documentcategories', $data)) {
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
			$this->DB->where('docCatID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'documentcategories', $data)) {
				return true;
			} else {
				return false;
			}
		}	
	}
	
?>