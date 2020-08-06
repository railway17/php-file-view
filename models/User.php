<?php

	class User {
		
		function __construct() {
			global $oDatabase;
			$this->DB = $oDatabase;
		}
		
		function generateUsername($username)
		{			
			$userList = $this->DB->rawQuery
			( "
				SELECT 			username
				FROM 			".DB_TBL_PREFIX."users
				WHERE			1=1
				AND 			username LIKE '".$username."%'
				AND 			isDeleted = 0
				ORDER BY 		username DESC
			" );
			
			if(count($userList) > 0) {
				$lastUsername = $userList[0]['username'];
				$lastIndexNum = str_replace($username, '', $lastUsername);
				if($lastIndexNum != '' && $lastIndexNum > 0) {
					$nextIndexNum = $lastIndexNum + 1;
				} else {
					$nextIndexNum = 1;
				}
				return $username.$nextIndexNum;
			} else {
				return $username;
			}
		}
		
		function countUsers(){
			$sql =
			"
				SELECT			COUNT(*) AS 'userCount'
				FROM			".DB_TBL_PREFIX."users
				WHERE			1=1
				AND				userID NOT IN (-1,16,27,45,74,75)
				AND				isDeleted = 0
			";

			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0]['userCount'];
			} else {
				return 0;
			}
		}

		/*
		function getAll($dataSet = 'default', $extraSQL = '', $orderBySQL = 'ORDER BY u.userID ASC') {
			$filter = array();
			$filterSQL = NULL;

			if($dataSet == 'showAll') {
				$filter[] = "AND			u.isDeleted IN(0,1)";
				$filter[] = "AND			u.userID <> -1";
				$filter[] = "AND			u.isCGSAdmin = 0";
			} elseif($dataSet == 'getAll') {
				$filter[] = "AND			u.isDeleted = 0";
			} else {
				$filter[] = "AND			u.isDeleted = 0";
				$filter[] = "AND			u.isCGSAdmin = 0";
			}

			$filterSQL = implode("\n\t\t\t\t",$filter);
			
			$sql =
			"
				SELECT			u.userID, e.employeeID, isExternal, u.password, u.salt, u.username, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS 'fullName', IF(u.isExternal = 1, u.forename, AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."')) AS 'forename', IF(u.isExternal = 1, u.surname, AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."')) AS 'surname', IF(u.isExternal = 1, u.email, AES_DECRYPT(e.workEmail, '".ENCRYPTION_KEY."')) AS 'email', IF(u.isExternal = 1, u.email, AES_DECRYPT(e.workEmail, '".ENCRYPTION_KEY."')) AS 'workEmail', IF(u.isExternal = 1, u.extNum, e.extNum) AS 'extNum', IF(u.isExternal = 1, u.position, AES_DECRYPT(e.jobTitle, '".ENCRYPTION_KEY."')) AS 'position', IF(u.isExternal = 1, u.employeeTypeID, e.employeeTypeID) AS 'employeeTypeID', et.name AS employeeTypeName, IF(u.isExternal = 1, u.companyID, e.companyID) AS 'companyID', IF(u.isExternal = 1, u.depotID, e.depotID) AS 'depotID', d.name AS depotName, IF(u.isExternal = 1, u.departmentID, e.departmentID) AS 'departmentID', d2.name AS departmentName, AES_DECRYPT(e.address, '".ENCRYPTION_KEY."') AS 'address', AES_DECRYPT(e.town, '".ENCRYPTION_KEY."') AS 'town', AES_DECRYPT(e.city, '".ENCRYPTION_KEY."') AS 'city', AES_DECRYPT(e.postcode, '".ENCRYPTION_KEY."') AS 'postcode', AES_DECRYPT(e.telephone, '".ENCRYPTION_KEY."') AS 'telephone', AES_DECRYPT(e.mobile, '".ENCRYPTION_KEY."') AS 'mobile', AES_DECRYPT(e.workMobile, '".ENCRYPTION_KEY."') AS 'workMobile', AES_DECRYPT(e.emergencyContact, '".ENCRYPTION_KEY."') AS 'emergencyContact', AES_DECRYPT(e.contactRelationship, '".ENCRYPTION_KEY."') AS 'contactRelationship', AES_DECRYPT(e.contactNumber, '".ENCRYPTION_KEY."') AS 'contactNumber', u.hasMultiCompanyAccess, u.signature, u.groupPermissions, AES_DECRYPT(u.securityPhone, '".ENCRYPTION_KEY."') AS 'securityPhone', e.isDeleted AS 'employeeDeleted', e.workEndDate AS 'employeeWorkEndDate'
				FROM 			".DB_TBL_PREFIX."users AS u
				LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.userID = u.userID
				LEFT JOIN 		".DB_TBL_PREFIX."depots AS d ON IF(u.isExternal = 1, d.depotID = u.depotID, d.depotID = e.depotID)
				LEFT JOIN 		".DB_TBL_PREFIX."departments AS d2 ON IF(u.isExternal = 1, d2.departmentID = u.departmentID, d2.departmentID = e.departmentID)
				LEFT JOIN 		".DB_TBL_PREFIX."employeetypes AS et ON IF(u.isExternal = 1, et.employeeTypeID = u.employeeTypeID, et.employeeTypeID = e.employeeTypeID)
				WHERE			1=1
				".$filterSQL."
                ".$extraSQL."
				ORDER BY 		IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) ASC
			";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		*/

		function getAll() {
			$sql =
			"
				SELECT			u.userID, isExternal, u.username, u.forename, u.surname, u.email, u.extNum, u.position, u.groupID
				FROM 			".DB_TBL_PREFIX."users AS u
				WHERE			isDeleted=0
			";
            
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}

		function getOne($userID) {
			$sql = "
				SELECT			u.userID, e.employeeID, isExternal, u.password, u.salt, u.username, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS 'fullName', IF(u.isExternal = 1, u.forename, AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."')) AS 'forename', IF(u.isExternal = 1, u.surname, AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."')) AS 'surname', IF(u.isExternal = 1, u.email, AES_DECRYPT(e.workEmail, '".ENCRYPTION_KEY."')) AS 'email', IF(u.isExternal = 1, u.email, AES_DECRYPT(e.workEmail, '".ENCRYPTION_KEY."')) AS 'workEmail', IF(u.isExternal = 1, u.extNum, e.extNum) AS 'extNum', IF(u.isExternal = 1, u.position, AES_DECRYPT(e.jobTitle, '".ENCRYPTION_KEY."')) AS 'position', IF(u.isExternal = 1, u.employeeTypeID, e.employeeTypeID) AS 'employeeTypeID', et.name AS employeeTypeName, IF(u.isExternal = 1, u.companyID, e.companyID) AS 'companyID', IF(u.isExternal = 1, u.depotID, e.depotID) AS 'depotID', d.name AS depotName, IF(u.isExternal = 1, u.departmentID, e.departmentID) AS 'departmentID', d2.name AS departmentName, AES_DECRYPT(e.address, '".ENCRYPTION_KEY."') AS 'address', AES_DECRYPT(e.town, '".ENCRYPTION_KEY."') AS 'town', AES_DECRYPT(e.city, '".ENCRYPTION_KEY."') AS 'city', AES_DECRYPT(e.postcode, '".ENCRYPTION_KEY."') AS 'postcode', AES_DECRYPT(e.telephone, '".ENCRYPTION_KEY."') AS 'telephone', AES_DECRYPT(e.mobile, '".ENCRYPTION_KEY."') AS 'mobile', AES_DECRYPT(e.workMobile, '".ENCRYPTION_KEY."') AS 'workMobile', AES_DECRYPT(e.emergencyContact, '".ENCRYPTION_KEY."') AS 'emergencyContact', AES_DECRYPT(e.contactRelationship, '".ENCRYPTION_KEY."') AS 'contactRelationship', AES_DECRYPT(e.contactNumber, '".ENCRYPTION_KEY."') AS 'contactNumber', u.hasMultiCompanyAccess, u.signature, u.groupPermissions, AES_DECRYPT(u.securityPhone, '".ENCRYPTION_KEY."') AS 'securityPhone', e.isDeleted AS 'employeeDeleted', e.workEndDate AS 'employeeWorkEndDate'
				FROM 			".DB_TBL_PREFIX."users AS u
				LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.userID = u.userID
				LEFT JOIN 		".DB_TBL_PREFIX."depots AS d ON IF(u.isExternal = 1, d.depotID = u.depotID, d.depotID = e.depotID)
				LEFT JOIN 		".DB_TBL_PREFIX."departments AS d2 ON IF(u.isExternal = 1, d2.departmentID = u.departmentID, d2.departmentID = e.departmentID)
				LEFT JOIN 		".DB_TBL_PREFIX."employeetypes AS et ON IF(u.isExternal = 1, et.employeeTypeID = u.employeeTypeID, et.employeeTypeID = e.employeeTypeID)
				WHERE 			1=1
				AND 			u.userID = ".$userID."
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
        
        function checkUserPermissionGroup($filterSQL = '') {
			$sql =
			"
				SELECT			userID, username, forename, surname, CONCAT(forename, ' ', surname) AS 'name', email, position, departmentID, depotID, signature, groupPermissions, isDeleted
				FROM 			".DB_TBL_PREFIX."users
				WHERE			1=1
				".$filterSQL."
				ORDER BY 		CONCAT(surname, ' ', forename) ASC
			";

			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}

		function checkUserExists($username = '') {
			$sql =
			"
				SELECT			userID, username, forename, surname, CONCAT(forename, ' ', surname) AS 'name', email, position, departmentID, depotID, signature, isDeleted
				FROM 			".DB_TBL_PREFIX."users
				WHERE			1=1
				AND  			username = '".$username."'
			";

			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return true;
			} else {
				return false;
			}
		}
		
		function countUserBasedOnType($userType = '') {
			
			if($userType == 'internal') {
				$extraSQL = " AND u.isExternal = 0 ";
			} elseif($userType == 'external') {
				$extraSQL = " AND u.isExternal = 1 AND u.isCGSAdmin = 0";
			} elseif($userType == 'cgs') {
				$extraSQL = " AND u.isCGSAdmin = 1";
			} elseif($userType == 'all') {
				//dont need to do anything
			}
			
			$sql =
			"
				SELECT			COUNT(*) as 'count'
				FROM 			".DB_TBL_PREFIX."users AS u
				WHERE			1=1
				AND 			u.isDeleted = 0
				".$extraSQL."
			";

			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0]['count'];
			} else {
				return 0;
			}
		}
		
		function getTMSUsers() {
			
			$sql =
			"
				SELECT			COUNT(*) as 'count'
				FROM 			".DB_TBL_PREFIX."users AS u
				WHERE			1=1
				AND 			u.isDeleted = 0
				AND 			u.isCGSAdmin = 0
			";

			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0]['count'];
			} else {
				return 0;
			}
		}
		
		function getAllHistoric($dataSet = 'default') {
			$filter = array();
			$filterSQL = NULL;

			if($dataSet == 'showAll') {
				$filter[] = "AND			u.isDeleted IN(0,1)";
				$filter[] = "AND			u.userID <> -1";
				$filter[] = "AND			u.isCGSAdmin = 0";
			} elseif($dataSet == 'getAll') {
				$filter[] = "AND			u.isDeleted = 0";
			} else {
				$filter[] = "AND			u.isDeleted = 0";
				$filter[] = "AND			u.isCGSAdmin = 0";
			}

			$filterSQL = implode("\n\t\t\t\t",$filter);

			$sql =
			"
				SELECT			u.userID, isExternal, u.username, u.password, u.salt, CONCAT(u.forename, ' ', u.surname) AS 'fullName', u.forename, u.surname,  u.email, u.extNum, u.position, u.employeeTypeID, et.name AS employeeTypeName, u.depotID, d.name AS depotName, u.departmentID, d2.name AS departmentName, u.hasMultiCompanyAccess, u.signature, u.groupPermissions, u.isDeleted
				FROM 			".DB_TBL_PREFIX."users AS u
				LEFT JOIN 		".DB_TBL_PREFIX."depots AS d ON d.depotID = u.depotID
				LEFT JOIN 		".DB_TBL_PREFIX."departments AS d2 ON d2.departmentID = u.departmentID
				LEFT JOIN 		".DB_TBL_PREFIX."employeetypes AS et ON et.employeeTypeID = u.employeeTypeID
				WHERE			1=1
				".$filterSQL."
				ORDER BY 		CONCAT(u.forename, ' ', u.surname) ASC
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
		function customSQL($sql, $return_id = false, $bindParams = null) {
			$id = $this->DB->rawReturnID($sql, $bindParams, $return_id);
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
		
        function insert($data, $return_id = false) {
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'users', $data);
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
		
		function update($data, $id) {
			$this->DB->where('userID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'users', $data)) {
				if($this->DB->count > 0) {
					return true;
				} else {
					return false;
				}
			} else {
				return $this->DB->getLastError();
			}
		}

		function delete($id) {
			$data = array('isDeleted' => 1);
			$this->DB->where('userID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'users', $data)) {
				return true;
			} else {
				return false;
			}
		}
	}

?>