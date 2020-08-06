<?php

	class Status
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
				SELECT			statusID, relTypeID, name, displayOrder, isDeleted
				FROM 			".DB_TBL_PREFIX."statuses
				WHERE			1=1
				".$extraSQL."
				AND 			isDeleted = 0
				ORDER BY		relTypeID, displayOrder ASC
			";

			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
		function getOne($statusID)
		{
			$sql = 
			"
				SELECT			statusID, relTypeID, name, displayOrder
				FROM			".DB_TBL_PREFIX."statuses
				WHERE			statusID = ".$statusID."
				AND  			isDeleted = 0
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
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'statuses', $data);
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
			$this->DB->where('statusID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'statuses', $data)) {
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
			$this->DB->where('statusID', $id);
			if ($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'statuses', $data)) {
				return true;
			} else {
				return false;
			}
		}	
	}
	
?>