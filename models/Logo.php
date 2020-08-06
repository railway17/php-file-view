<?php

	class Logo
	{
		function __construct()
		{
			global $oDatabase;
			$this->DB = $oDatabase;
		}
		
		function countLogos($extraSQL = '')
		{
			$sql = 
			"
				SELECT			COUNT(l.logoID) AS 'totalLogos'
				FROM			".DB_NAME.".".DB_TBL_PREFIX."logos AS l
				WHERE			1=1
				".$extraSQL."
				AND				l.isDeleted = 0
			";
			$stmt = $this->DB->rawQuery($sql);
			if($stmt[0]['totalLogos'] > 0){
				return $stmt[0]['totalLogos'];
			} else {
				return 0;
			}
		}
		
		function getAll($extraSQL = '')
		{
			$sql = 
			"
				SELECT			l.logoID, l.logoTypeID, l.logoName, l.template, l.companyID, l.description, l.isCompanyLogo, l.documentID, l.thumbDocumentID, l.createdBy, DATE_FORMAT(l.createdDate,'%d/%m/%Y') AS 'createdDate', l.createdDate AS 'mysql_createdDateTime'
				FROM 			".DB_NAME.".".DB_TBL_PREFIX."logos AS l
				WHERE			1=1
				AND 			l.isDeleted = 0
				".$extraSQL."
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0){
				return $stmt;
			} else {
				return false;
			}
		}
        
         
        function getCompanyReportHeaderLogo($companyID)
		{
			$sql = 
			"
				SELECT			l.logoID, l.logoName AS 'name', CONCAT (AES_DECRYPT(d.docFilePath, '".ENCRYPTION_KEY."'), AES_DECRYPT(d.docFileName, '".ENCRYPTION_KEY."')) AS 'link'
				FROM 			".DB_NAME.".".DB_TBL_PREFIX."logos AS l
                LEFT JOIN		".DB_TBL_PREFIX."documents AS d ON d.documentID = l.documentID
				WHERE			1=1
				AND 			l.isDeleted = 0
				AND 			l.logoTypeID = 1
				AND 			l.companyID = ".$companyID."
                ORDER BY        l.createdDate  ASC
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0){
				return $stmt[0];
			} else {
				return false;
			}
		}
        
        function getCompanyEmailFooterLogo($companyID)
		{
			$sql = 
			"
				SELECT			l.logoID, l.logoName AS 'name', CONCAT (AES_DECRYPT(d.docFilePath, '".ENCRYPTION_KEY."'), AES_DECRYPT(d.docFileName, '".ENCRYPTION_KEY."')) AS 'link'
				FROM 			".DB_NAME.".".DB_TBL_PREFIX."logos AS l
                LEFT JOIN		".DB_TBL_PREFIX."documents AS d ON d.documentID = l.documentID
				WHERE			1=1
				AND 			l.isDeleted = 0
				AND 			l.logoTypeID = 3
				AND 			l.companyID = ".$companyID."
                ORDER BY        l.createdDate  ASC
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0){
				return $stmt[0];
			} else {
				return false;
			}
		}
        
        function getEmailFooterLogos($companyID)
		{
			$sql = 
			"
				SELECT			l.logoID, l.logoName AS 'name', CONCAT (AES_DECRYPT(d.docFilePath, '".ENCRYPTION_KEY."'), AES_DECRYPT(d.docFileName, '".ENCRYPTION_KEY."')) AS 'link'
				FROM 			".DB_NAME.".".DB_TBL_PREFIX."logos AS l
                LEFT JOIN		".DB_TBL_PREFIX."documents AS d ON d.documentID = l.documentID
				WHERE			1=1
				AND 			l.isDeleted = 0
				AND 			l.logoTypeID = 2
				AND 			l.companyID = ".$companyID."
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0){
				return $stmt;
			} else {
				return false;
			}
		}
		
		function getOne($logoID)
		{
			$sql = 
			"
				SELECT			l.logoID, l.logoTypeID, l.logoName, l.template, l.companyID, l.description, l.isCompanyLogo, l.documentID, l.thumbDocumentID, l.createdBy, DATE_FORMAT(l.createdDate,'%d/%m/%Y') AS 'createdDate', l.createdDate AS 'mysql_createdDateTime'
				FROM 			".DB_NAME.".".DB_TBL_PREFIX."logos AS l
				WHERE			1=1
				AND			    l.logoID = ".$logoID."
				AND 			l.isDeleted = 0
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0){
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
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'logos', $data);
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
			$this->DB->where('logoID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'logos', $data)) {
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
			$this->DB->where('logoID', $id);
			if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'logos', $data)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
?>