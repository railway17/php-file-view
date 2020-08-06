<?php

	class Depot
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
				SELECT			d.depotID, d.companyID, d.name, d.addressLine1, d.addressLine2, d.city, d.county, d.postcode, CONCAT(d.addressLine1, ', ', d.addressLine2, ', ', d.city, ', ', county, ', ', d.postcode) AS 'depotAddress', d.phone, d.emergencyContactID, CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."')) AS 'contactName', d.emergencyContactPhone, outHoursNumber, d.nearMissNumber, d.latitude, d.longitude, DATE_FORMAT(d.createdDate,'%d/%m/%Y') AS 'createdDate', d.createdBy, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e2.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e2.surname, '".ENCRYPTION_KEY."'))) AS 'createdByName'
				FROM			".DB_TBL_PREFIX."depots AS d
				LEFT JOIN		".DB_TBL_PREFIX."employees AS e ON e.employeeID = d.emergencyContactID
				LEFT JOIN 		".DB_TBL_PREFIX."users AS u ON u.userID = d.createdBy
				LEFT JOIN 		".DB_TBL_PREFIX."employees AS e2 ON e2.userID = d.createdBy AND u.isExternal = 0
				WHERE			1=1
				".$extraSQL."
				AND				d.isDeleted = 0
                ORDER BY        d.name ASC
			";
			
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
	
		function getOne($depotID)
		{
			$sql = 
			"
				SELECT			d.depotID, d.companyID, d.name, d.addressLine1, d.addressLine2, d.city, d.county, d.postcode, CONCAT(d.addressLine1, ', ', d.addressLine2, ', ', d.city, ', ', county, ', ', d.postcode) AS 'depotAddress', d.phone, d.emergencyContactID, CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."')) AS 'contactName', d.emergencyContactPhone, outHoursNumber, d.nearMissNumber, d.latitude, d.longitude, DATE_FORMAT(d.createdDate,'%d/%m/%Y') AS 'createdDate', d.createdBy, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e2.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e2.surname, '".ENCRYPTION_KEY."'))) AS 'createdByName'
				FROM			".DB_TBL_PREFIX."depots AS d
				LEFT JOIN		".DB_TBL_PREFIX."employees AS e ON e.employeeID = d.emergencyContactID
				LEFT JOIN 		".DB_TBL_PREFIX."users AS u ON u.userID = d.createdBy
				LEFT JOIN 		".DB_TBL_PREFIX."employees AS e2 ON e2.userID = d.createdBy AND u.isExternal = 0
				WHERE			1=1
				AND				d.depotID = ".$depotID."
			";
			
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
		function countDepots($extraSQL = '')
		{
			$sql = 
			"
				SELECT			COUNT(d.depotID) AS 'totalDepots'
				FROM			".DB_TBL_PREFIX."depots AS d
				WHERE			1=1
				".$extraSQL."
				AND				d.isDeleted = 0
			";
			$stmt = $this->DB->rawQuery($sql);
			if($stmt[0]['totalDepots'] > 0){
				return $stmt[0]['totalDepots'];
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
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'depots', $data);
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
			$this->DB->where('depotID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'depots', $data)) {
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
			$this->DB->where('depotID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'depots', $data)) {
				return true;
			} else {
				return false;
			}
		}	
	}
	
?>