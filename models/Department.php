<?php

	class Department
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
				SELECT			d.departmentID, d.companyID, d.depotID, d.name,  AES_DECRYPT(c.companyName, '".ENCRYPTION_KEY."') AS 'companyName', d2.name AS 'depotName', d.managerIDs, d.supervisorIDs, AES_DECRYPT(d.email, '".ENCRYPTION_KEY."') AS 'email', d.extNum
				FROM			".DB_TBL_PREFIX."departments as d
                LEFT JOIN       ".DB_TBL_PREFIX."company as c on c.companyID = d.companyID
                LEFT JOIN       ".DB_TBL_PREFIX."depots as d2 on d2.depotID = d.depotID
				WHERE			1=1
				".$extraSQL."
				AND				d.isDeleted = 0
			";
			
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
	
		function getOne($departmentID)
		{
			$sql = 
			"
				SELECT			d.departmentID, d.companyID, d.depotID, d.name,  AES_DECRYPT(c.companyName, '".ENCRYPTION_KEY."') AS 'companyName', d2.name AS 'depotName', d.managerIDs, d.supervisorIDs, AES_DECRYPT(d.email, '".ENCRYPTION_KEY."') AS 'email', d.extNum
				FROM			".DB_TBL_PREFIX."departments as d
                LEFT JOIN       ".DB_TBL_PREFIX."company as c on c.companyID = d.companyID
                LEFT JOIN       ".DB_TBL_PREFIX."depots as d2 on d2.depotID = d.depotID
				WHERE			1=1
				AND				d.departmentID = ".$departmentID;
			
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
        
        function getOperationsDepartment($companyID)
		{
			$sql = 
			"
				SELECT			d.departmentID, d.name, d.depotID, d2.companyID, d.managerIDs, d.supervisorIDs, AES_DECRYPT(d.email, '".ENCRYPTION_KEY."') AS 'email', d.extNum
				FROM			".DB_TBL_PREFIX."departments as d
                LEFT JOIN       ".DB_TBL_PREFIX."depots as d2 on d2.depotID = d.depotID
				WHERE			1=1
				AND				d.departmentID IN (SELECT operationsDepartmentID FROM ".DB_TBL_PREFIX."company WHERE companyID = ".$companyID.")
            ";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
        
        function getHRDepartment($companyID)
		{
			$sql = 
			"
				SELECT			d.departmentID, d.name, d.depotID, d2.companyID, d.managerIDs, d.supervisorIDs, AES_DECRYPT(d.email, '".ENCRYPTION_KEY."') AS 'email', d.extNum
				FROM			".DB_TBL_PREFIX."departments as d
                LEFT JOIN       ".DB_TBL_PREFIX."depots as d2 on d2.depotID = d.depotID
				WHERE			1=1
				AND				d.departmentID IN (SELECT hrDepartmentID FROM ".DB_TBL_PREFIX."company WHERE companyID = ".$companyID.")
            ";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
        
        function countDepartments($extraSQL = '')
		{
			$sql = 
			"
				SELECT			COUNT(d.departmentID) AS 'totalDepartments'
				FROM			".DB_TBL_PREFIX."departments AS d
                LEFT JOIN       ".DB_TBL_PREFIX."depots as d2 on d2.depotID = d.depotID
				WHERE			1=1
				".$extraSQL."
				AND				d.isDeleted = 0
			";
			$stmt = $this->DB->rawQuery($sql);
			if($stmt[0]['totalDepartments'] > 0){
				return $stmt[0]['totalDepartments'];
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
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'departments', $data);
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
			$this->DB->where('departmentID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'departments', $data)) {
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
			$this->DB->where('departmentID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'departments', $data)) {
				return true;
			} else {
				return false;
			}
		}	
	}
	
?>