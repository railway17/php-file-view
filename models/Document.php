<?php

	class Document
	{
		function __construct()
		{
			global $oDatabase;
			$this->DB = $oDatabase;
		}

		function getAll($extraSQL = '', $orderBy = '')
		{
			// $sql =
			// "
			// 	SELECT			d.documentID, AES_DECRYPT(d.title, '".ENCRYPTION_KEY."') AS 'title', d.isCustomer, d.docCatID, dc.name AS 'docCatName', AES_DECRYPT(d.docFilePath, '".ENCRYPTION_KEY."') AS 'docFilePath', AES_DECRYPT(d.docFileName, '".ENCRYPTION_KEY."') AS 'docFileName', d.relationID, d.relTypeID, d.attachQuote, d.localAuthAppTypeID, d.includeVanPack, d.isCADPlan, d.expiryDate, DATE_FORMAT(d.expiryDate,'%d/%m/%Y') AS 'expiryDate', DATE_FORMAT(d.uploadedDate, '%Y-%m-%d') AS 'mysql_uploadedDate', DATE_FORMAT(d.uploadedDate,'%d/%m/%Y %H:%i:%s') AS 'uploadedDate', d.uploadedBy,IF(d.isCustomer = 1, CONCAT(cl.forename, ' ', cl.surname),IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."')))) AS 'uploadedByName', d.isConfidential, d.displayOrder, d.isDeleted
			// 	FROM			".DB_TBL_PREFIX."documents AS d
			// 	LEFT JOIN		".DB_TBL_PREFIX."documentcategories AS dc ON dc.docCatID = d.docCatID
			// 	LEFT JOIN		".DB_TBL_PREFIX."localauthorityapplicationtypes AS laat ON laat.localAuthAppTypeID = d.localAuthAppTypeID
			// 	LEFT JOIN		".DB_TBL_PREFIX."users AS u ON u.userID = d.uploadedBy
			// 	LEFT JOIN 		".DB_TBL_PREFIX."customerlogins AS cl ON cl.loginID = d.uploadedBy
			// 	LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.userID = d.uploadedBy AND u.isExternal = 0
			// 	WHERE			1=1
			// 	".$extraSQL."
			// 	AND				d.isDeleted = 0
			// 	".$orderBy."
			// ";
			$sql =
			"
				SELECT			D.*, F.folderPath, F.folderName, CONCAT(U.forename, ' ', U.surname) AS OwnerName
				FROM			".DB_TBL_PREFIX."documents AS D
				INNER JOIN		".DB_TBL_PREFIX."doc_folders AS F ON D.folderId=F.folderId
				INNER JOIN		".DB_TBL_PREFIX."users AS U ON U.userID=D.ownerId
				WHERE			1=1
				".$extraSQL."
				AND				D.isDeleted = 0
				".$orderBy."
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}

		function getOne($documentID)
		{
			// $sql =
			// "
			// 	SELECT			d.documentID, AES_DECRYPT(d.title, '".ENCRYPTION_KEY."') AS 'title', d.isCustomer, d.docCatID, dc.name AS 'docCatName', AES_DECRYPT(d.docFilePath, '".ENCRYPTION_KEY."') AS 'docFilePath', AES_DECRYPT(d.docFileName, '".ENCRYPTION_KEY."') AS 'docFileName', d.relationID, d.relTypeID, d.attachQuote, d.localAuthAppTypeID, d.includeVanPack, d.isCADPlan, d.expiryDate, DATE_FORMAT(d.expiryDate,'%d/%m/%Y') AS 'expiryDate', DATE_FORMAT(d.uploadedDate, '%Y-%m-%d') AS 'mysql_uploadedDate', DATE_FORMAT(d.uploadedDate,'%d/%m/%Y %H:%i:%s') AS 'uploadedDate', d.uploadedBy,IF(d.isCustomer = 1, CONCAT(cl.forename, ' ', cl.surname),IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."')))) AS 'uploadedByName', d.isConfidential, d.displayOrder, d.isDeleted
			// 	FROM			".DB_TBL_PREFIX."documents AS d
			// 	LEFT JOIN		".DB_TBL_PREFIX."documentcategories AS dc ON dc.docCatID = d.docCatID
			// 	LEFT JOIN		".DB_TBL_PREFIX."localauthorityapplicationtypes AS laat ON laat.localAuthAppTypeID = d.localAuthAppTypeID
			// 	LEFT JOIN		".DB_TBL_PREFIX."users AS u ON u.userID = d.uploadedBy
			// 	LEFT JOIN 		".DB_TBL_PREFIX."customerlogins AS cl ON cl.loginID = d.uploadedBy
			// 	LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.userID = d.uploadedBy AND u.isExternal = 0
			// 	WHERE			1=1
			// 	AND				d.documentID = ".$documentID."
			// 	AND				d.isDeleted = 0
			// ";

			$sql =
			"
				SELECT			D.*, F.folderPath, F.folderName, CONCAT(U.forename, ' ', U.surname) AS OwnerName
				FROM			".DB_TBL_PREFIX."documents AS D
				INNER JOIN		".DB_TBL_PREFIX."doc_folders AS F ON D.folderId = F.folderId
				INNER JOIN		".DB_TBL_PREFIX."users AS U ON U.userID=D.ownerId
				
				WHERE			1=1
				AND				D.documentID = ".$documentID."
				AND				D.isDeleted = 0 
			";
			
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				// return $stmt;
				return $stmt[0];
			} else {
				return false;
			}
		}

		function getByPath($docPath)
		{
			$sql =
			"
				SELECT			*
				FROM		".DB_TBL_PREFIX."documents 
				
				WHERE			1=1
				AND				docFilePath = '".$docPath."'
				AND				isDeleted = 0 
			";
			
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				// return $stmt;
				return $stmt[0];
			} else {
				return false;
			}
		}
		
		function getDocumentRelTypes()
		{
			$sql =
			"
				SELECT			DISTINCT d.relTypeID, AES_DECRYPT(d.title, '".ENCRYPTION_KEY."') AS 'title', d.isCustomer, d.docCatID, dc.name AS 'docCatName', AES_DECRYPT(d.docFilePath, '".ENCRYPTION_KEY."') AS 'docFilePath', AES_DECRYPT(d.docFileName, '".ENCRYPTION_KEY."') AS 'docFileName', d.relationID, d.relTypeID, d.attachQuote, d.localAuthAppTypeID, d.includeVanPack, d.isCADPlan, d.expiryDate, DATE_FORMAT(d.expiryDate,'%d/%m/%Y') AS 'expiryDate', DATE_FORMAT(d.uploadedDate, '%Y-%m-%d') AS 'mysql_uploadedDate', DATE_FORMAT(d.uploadedDate,'%d/%m/%Y %H:%i:%s') AS 'uploadedDate', d.uploadedBy, IF(d.isCustomer = 1, CONCAT(cl.forename, ' ', cl.surname), CONCAT(u.forename, ' ', u.surname)) AS 'uploadedByName', d.isConfidential, d.displayOrder, d.isDeleted
				FROM			".DB_TBL_PREFIX."documents AS d
				WHERE			1=1
				AND				d.isDeleted = 0
			";

			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}	
		}
		
		function countDocuments($extraSQL = '')
		{
			$sql =
			"
				SELECT			COUNT(d.documentID) AS 'Documents'
				FROM			".DB_TBL_PREFIX."documents AS d
				WHERE			1=1
				".$extraSQL."
				AND				d.isDeleted = 0
			";

			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0]['Documents'];
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
            $id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'documents', $data);
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
            $this->DB->where('documentID', $id);
            if($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'documents', $data)) {
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
            $this->DB->where('documentID', $id);
            if ($this->DB->update(DB_NAME.'.'.DB_TBL_PREFIX.'documents', $data)) {
                return true;
            } else {
                return false;
            }
        }
	}

?>