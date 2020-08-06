<?php

	class EmailLog
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
				SELECT 				el.logID, el.dateTime, el.relTypeID, el.relationID, el.sender, el.recipients, el.subject, el.body, el.attachments, el.status, el.response, el.sentBy, el.isDeleted, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS  'sentByName'
				FROM 				".DB_TBL_PREFIX."emaillogs AS el
                LEFT JOIN 		    ".DB_TBL_PREFIX."reltypes AS rt ON rt.relTypeID = el.relTypeID	
                LEFT JOIN 			".DB_TBL_PREFIX."users as u on u.userID = el.sentBy
                LEFT JOIN 			".DB_TBL_PREFIX."employees AS e ON e.userID = el.sentBy AND u.isExternal = 0
                WHERE 				1=1
			";
					  
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
		function getOne($logID)
		{
			$sql = 
			"
                SELECT 				el.logID, el.dateTime, el.relTypeID, el.relationID, el.sender, el.recipients, el.subject, el.body, el.attachments, el.status, el.response, el.sentBy, el.isDeleted, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS  'sentByName'
				FROM 				".DB_TBL_PREFIX."emaillogs AS el
                LEFT JOIN 		    ".DB_TBL_PREFIX."reltypes AS rt ON rt.relTypeID = el.relTypeID	
                LEFT JOIN 			".DB_TBL_PREFIX."users as u on u.userID = el.sentBy
                LEFT JOIN 			".DB_TBL_PREFIX."employees AS e ON e.userID = el.sentBy AND u.isExternal = 0
				WHERE 				1=1
				AND 				el.logID = ".$logID."
			";
					  
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
		function getAllEmailLogs($logID)
		{
			$sql = 
			"
                SELECT 				el.logID, el.dateTime, el.relTypeID, el.relationID, el.sender, el.recipients, el.subject, el.body, el.attatchments, el.status, el.response, el.createdBy, el.isDeleted,IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS  'sentByName'
				FROM 				".DB_TBL_PREFIX."emaillogs AS el
                LEFT JOIN 		    ".DB_TBL_PREFIX."relTypeID AS rt ON rt.relTypeID = el.relTypeID	
                LEFT JOIN 			".DB_TBL_PREFIX."employees AS e ON e.userID = el.sentBy AND u.isExternal = 0
                LEFT JOIN 			".DB_TBL_PREFIX."users as u on u.userID = el.sentBy

				WHERE 				1=1
	
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
        
        function countLogs($extraSQL = '')
		{
			$sql = 
			"
				SELECT			COUNT(*) AS 'totalLogs'
				FROM			".DB_NAME.".".DB_TBL_PREFIX."emaillogs
				WHERE			1=1
				".$extraSQL."
				AND				isDeleted = 0
			";

			$stmt = $this->DB->rawQuery($sql);
			if($stmt[0]['totalLogs'] > 0) {
				return $stmt[0]['totalLogs'];
			} else {
				return 0;
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
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'emaillogs', $data);
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
			$this->DB->where('logID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'emaillogs', $data)) {
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
			$this->DB->where('logID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'emaillogs', $data)) {
				return true;
			} else {
				return false;
			}
		}	
	}
	
?>