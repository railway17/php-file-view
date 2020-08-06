<?php

	class PasswordReset
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
				SELECT			resetID, authCode, userID, email, userAgent, remoteIP, createdDate, status
				FROM 			".DB_TBL_PREFIX."passwordresets
				WHERE			1=1
				".$extraSQL."
			";

			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
		function getOne($resetID)
		{
			$sql = 
			"
				SELECT			resetID, authCode, userID, email, userAgent, remoteIP, createdDate, status
				FROM 			".DB_TBL_PREFIX."passwordresets
				WHERE			resetID = ".$resetID."
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
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'passwordresets', $data);
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
			$this->DB->where('resetID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'passwordresets', $data)) {
				if($this->DB->count > 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return $this->DB->getLastError();
			}
		}
		
		function delete($resetID)
		{			
			$stmt = $this->DB->rawQuery("DELETE FROM ".DB_NAME.'.'.DB_TBL_PREFIX."passwordresets WHERE 1=1 AND resetID = '".$resetID."'");
			if($stmt > 0) {
				return true;
			} else {
				return false;
			}
		}
	}
	
?>