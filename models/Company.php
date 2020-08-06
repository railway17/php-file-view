<?php

	class Company
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
				SELECT			c.companyID, AES_DECRYPT(c.companyName, '".ENCRYPTION_KEY."') AS 'companyName', AES_DECRYPT(c.abbrevCompanyName, '".ENCRYPTION_KEY."') AS 'abbrevCompanyName', AES_DECRYPT(c.addressLine1, '".ENCRYPTION_KEY."') AS 'addressLine1', AES_DECRYPT(c.addressLine2, '".ENCRYPTION_KEY."') AS 'addressLine2', AES_DECRYPT(c.addressLine3, '".ENCRYPTION_KEY."') AS 'addressLine3', AES_DECRYPT(c.addressLine4, '".ENCRYPTION_KEY."') AS 'addressLine4', AES_DECRYPT(c.addressLine5, '".ENCRYPTION_KEY."') AS 'addressLine5', c.latitude, c.longitude, AES_DECRYPT(c.invAddressLine1, '".ENCRYPTION_KEY."') AS 'invAddressLine1', AES_DECRYPT(c.invAddressLine2, '".ENCRYPTION_KEY."') AS 'invAddressLine2', AES_DECRYPT(c.invAddressLine3, '".ENCRYPTION_KEY."') AS 'invAddressLine3', AES_DECRYPT(c.invAddressLine4, '".ENCRYPTION_KEY."') AS 'invAddressLine4', AES_DECRYPT(c.invAddressLine5, '".ENCRYPTION_KEY."') AS 'invAddressLine5', AES_DECRYPT(c.phoneNumber, '".ENCRYPTION_KEY."') AS 'phoneNumber', AES_DECRYPT(c.outHoursNumber, '".ENCRYPTION_KEY."') AS 'outHoursNumber', AES_DECRYPT(c.faxNumber, '".ENCRYPTION_KEY."') AS 'faxNumber', AES_DECRYPT(c.webAddress, '".ENCRYPTION_KEY."') AS 'webAddress', AES_DECRYPT(c.infoEmailAddress, '".ENCRYPTION_KEY."') AS 'infoEmailAddress', AES_DECRYPT(c.salesEmailAddress, '".ENCRYPTION_KEY."') AS 'salesEmailAddress', AES_DECRYPT(c.supportEmailAddress, '".ENCRYPTION_KEY."') AS 'supportEmailAddress', AES_DECRYPT(c.invContactName, '".ENCRYPTION_KEY."') AS 'invContactName', AES_DECRYPT(c.invEmailAddress, '".ENCRYPTION_KEY."') AS 'invEmailAddress', AES_DECRYPT(c.vatNumber, '".ENCRYPTION_KEY."') AS 'vatNumber', AES_DECRYPT(c.companyNumber, '".ENCRYPTION_KEY."') AS 'companyNumber', c.vatRate, c.workPattern, c.logo, AES_DECRYPT(c.bankName, '".ENCRYPTION_KEY."') AS 'bankName', c.bankBranch, AES_DECRYPT(c.bankAccountNumber, '".ENCRYPTION_KEY."') AS 'bankAccountNumber', AES_DECRYPT(c.bankSortCode, '".ENCRYPTION_KEY."') AS 'bankSortCode', c.bankLastUpdated AS 'mysql_bankLastUpdatedDate',DATE_FORMAT(c.bankLastUpdated,'%d/%m/%Y') AS 'bankLastUpdatedDate', DATEDIFF(CURDATE(), c.bankLastUpdated) AS 'bankLastUpdatedDays', c.defSalesNomCodeID, c.defPurchNomCodeID, c.defTaxCodeID, c.quotePrefix, c.quoteStartFrom, c.quoteSuffix, c.workPrefix, c.workStartFrom, c.workSuffix, c.paPrefix, c.paStartFrom, c.paSuffix, c.ppPrefix, c.ppStartFrom, c.ppSuffix, c.siPrefix, c.siStartFrom, c.siSuffix, c.scPrefix, c.scStartFrom, c.scSuffix, c.poPrefix, c.poStartFrom, c.poSuffix, c.financePackage, c.jsonSageCredentials, c.jsonDimensionsCredentials, AES_DECRYPT(c.hrDocumentPassword, '".ENCRYPTION_KEY."') AS 'hrDocumentPassword', c.multiDepots, c.mainDepot, c.operationsDepartmentID, c.hrDepartmentID, DATE_FORMAT(c.lastUpdatedDate,'%d/%m/%Y') AS 'lastUpdatedDate', DATE_FORMAT(c.createdDate,'%d/%m/%Y') AS 'createdDate', c.createdDate AS 'mysql_createdDateTime'
				FROM 			".DB_TBL_PREFIX."company AS c
				WHERE			1=1
				".$extraSQL."
                AND             c.isDeleted = 0
				ORDER BY		c.companyID ASC
			";

			$stmt = $this->DB->rawQuery($sql);
			if (count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
        
        function getPermittedCompanies($permissionName, $action)
		{
			$sql =
			"
				SELECT			c.companyID, AES_DECRYPT(c.companyName, '".ENCRYPTION_KEY."') AS 'companyName', AES_DECRYPT(c.addressLine1, '".ENCRYPTION_KEY."') AS 'addressLine1', AES_DECRYPT(c.addressLine2, '".ENCRYPTION_KEY."') AS 'addressLine2', AES_DECRYPT(c.addressLine3, '".ENCRYPTION_KEY."') AS 'addressLine3', AES_DECRYPT(c.addressLine4, '".ENCRYPTION_KEY."') AS 'addressLine4', AES_DECRYPT(c.addressLine5, '".ENCRYPTION_KEY."') AS 'addressLine5'
				FROM 			".DB_TBL_PREFIX."company AS c
				WHERE			1=1
                AND c.companyID IN (
                    SELECT 				up.companyID
                    FROM 				".DB_TBL_PREFIX."userpermissions AS up
                    LEFT JOIN 			".DB_TBL_PREFIX."permissions AS p ON p.permissionID = up.permissionID
                    WHERE				1=1
                    AND                 p.name = '".$permissionName."'
                    AND                 up.userID = ".$_SESSION['userID']." 
                    AND                 up.".$action." = 1
                )
                AND             c.isDeleted = 0
				ORDER BY		c.companyID ASC
			";

			$stmt = $this->DB->rawQuery($sql);
			if (count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}

		function getOne($companyID)
		{
			$sql =
			"
				SELECT			c.companyID, AES_DECRYPT(c.companyName, '".ENCRYPTION_KEY."') AS 'companyName', AES_DECRYPT(c.abbrevCompanyName, '".ENCRYPTION_KEY."') AS 'abbrevCompanyName', AES_DECRYPT(c.addressLine1, '".ENCRYPTION_KEY."') AS 'addressLine1', AES_DECRYPT(c.addressLine2, '".ENCRYPTION_KEY."') AS 'addressLine2', AES_DECRYPT(c.addressLine3, '".ENCRYPTION_KEY."') AS 'addressLine3', AES_DECRYPT(c.addressLine4, '".ENCRYPTION_KEY."') AS 'addressLine4', AES_DECRYPT(c.addressLine5, '".ENCRYPTION_KEY."') AS 'addressLine5', c.latitude, c.longitude, AES_DECRYPT(c.invAddressLine1, '".ENCRYPTION_KEY."') AS 'invAddressLine1', AES_DECRYPT(c.invAddressLine2, '".ENCRYPTION_KEY."') AS 'invAddressLine2', AES_DECRYPT(c.invAddressLine3, '".ENCRYPTION_KEY."') AS 'invAddressLine3', AES_DECRYPT(c.invAddressLine4, '".ENCRYPTION_KEY."') AS 'invAddressLine4', AES_DECRYPT(c.invAddressLine5, '".ENCRYPTION_KEY."') AS 'invAddressLine5', AES_DECRYPT(c.phoneNumber, '".ENCRYPTION_KEY."') AS 'phoneNumber', AES_DECRYPT(c.outHoursNumber, '".ENCRYPTION_KEY."') AS 'outHoursNumber', AES_DECRYPT(c.faxNumber, '".ENCRYPTION_KEY."') AS 'faxNumber', AES_DECRYPT(c.webAddress, '".ENCRYPTION_KEY."') AS 'webAddress', AES_DECRYPT(c.infoEmailAddress, '".ENCRYPTION_KEY."') AS 'infoEmailAddress', AES_DECRYPT(c.salesEmailAddress, '".ENCRYPTION_KEY."') AS 'salesEmailAddress', AES_DECRYPT(c.supportEmailAddress, '".ENCRYPTION_KEY."') AS 'supportEmailAddress', AES_DECRYPT(c.invContactName, '".ENCRYPTION_KEY."') AS 'invContactName', AES_DECRYPT(c.invEmailAddress, '".ENCRYPTION_KEY."') AS 'invEmailAddress', AES_DECRYPT(c.vatNumber, '".ENCRYPTION_KEY."') AS 'vatNumber', AES_DECRYPT(c.companyNumber, '".ENCRYPTION_KEY."') AS 'companyNumber', c.vatRate, c.favouredESCustomerIDs, c.favouredTMCustomerIDs, c.favouredMECustomerIDs, c.favouredCPCustomerIDs, c.favouredSRNCustomerIDs, c.favouredSSCustomerIDs, c.workPattern, c.logo, AES_DECRYPT(c.bankName, '".ENCRYPTION_KEY."') AS 'bankName', c.bankBranch, AES_DECRYPT(c.bankAccountNumber, '".ENCRYPTION_KEY."') AS 'bankAccountNumber', AES_DECRYPT(c.bankSortCode, '".ENCRYPTION_KEY."') AS 'bankSortCode', c.bankLastUpdated AS 'mysql_bankLastUpdatedDate',DATE_FORMAT(c.bankLastUpdated,'%d/%m/%Y') AS 'bankLastUpdatedDate', DATEDIFF(CURDATE(), c.bankLastUpdated) AS 'bankLastUpdatedDays', c.defSalesNomCodeID, c.defPurchNomCodeID, c.defTaxCodeID, c.quotePrefix, c.quoteStartFrom, c.quoteSuffix, c.workPrefix, c.workStartFrom, c.workSuffix, c.paPrefix, c.paStartFrom, c.paSuffix, c.ppPrefix, c.ppStartFrom, c.ppSuffix, c.siPrefix, c.siStartFrom, c.siSuffix, c.scPrefix, c.scStartFrom, c.scSuffix, c.poPrefix, c.poStartFrom, c.poSuffix, c.financePackage, c.jsonSageCredentials, c.jsonDimensionsCredentials, AES_DECRYPT(c.hrDocumentPassword, '".ENCRYPTION_KEY."') AS 'hrDocumentPassword', c.multiDepots, c.mainDepot, c.operationsDepartmentID, c.hrDepartmentID, DATE_FORMAT(c.lastUpdatedDate,'%d/%m/%Y') AS 'lastUpdatedDate', DATE_FORMAT(c.createdDate,'%d/%m/%Y') AS 'createdDate', c.createdDate AS 'mysql_createdDateTime'
				FROM 			".DB_TBL_PREFIX."company AS c
				WHERE			c.companyID = ".$companyID."
			";

			$stmt = $this->DB->rawQuery($sql);
			if (count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
        
        function getFavouredCustomers($companyID){
            $sql =
			"
				SELECT			c.favouredESCustomerIDs, c.favouredTMCustomerIDs, c.favouredMECustomerIDs, c.favouredCPCustomerIDs, c.favouredSRNCustomerIDs
				FROM 			".DB_TBL_PREFIX."company AS c
				WHERE			c.companyID = ".$companyID."
			";

			$stmt = $this->DB->rawQuery($sql);
			if (count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}    
        }

		function getDVLASearchStatistics($companyID){
			$sql = 
            "
                SELECT          totalDVLASearchCredits, usedDVLASearchCredits, SUM(totalDVLASearchCredits - usedDVLASearchCredits) AS 'remainingCredits'    
                FROM            ".DB_TBL_PREFIX."company 
                WHERE           companyID = ".$companyID;
            
			$stmt = $this->DB->rawQuery($sql);
			if (count($stmt) > 0) {
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
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'company', $data);
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
			$this->DB->where('companyID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'company', $data)) {
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
			$this->DB->where('companyID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'company', $data)) {
				return true;
			} else {
				return false;
			}
		}
	}
?>