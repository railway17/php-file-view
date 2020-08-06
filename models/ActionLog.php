<?php

	class ActionLog
	{
		function __construct()
		{
			global $oDatabase;
			$this->DB = $oDatabase;
		}

		function getAll($extraSQL = NULL, $orderBySQL = 'ORDER BY al.userID, al.dateTime ASC')
		{
			$sql = 
			"
				SELECT			logID, al.userID, u.username, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS 'userName', DATE_FORMAT(dateTime, '%d/%m/%Y - %H:%i:%s') AS 'dateTime', DATE_FORMAT(al.dateTime, '%Y-%m-%d') AS 'date', DATE_FORMAT(al.dateTime, '%H:%i:%s') AS 'time', al.action, al.sql
				FROM			".DB_TBL_PREFIX."actionlogs AS al
				LEFT JOIN 		".DB_TBL_PREFIX."users AS u ON u.userID = al.userID
				LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.userID = al.userID AND u.isExternal = 0
				WHERE			1=1
				".$extraSQL."
				".$orderBySQL."
			";
			
			$stmt = $this->DB->rawQuery( $sql );
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
				SELECT			logID, al.userID, u.username, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS  'userName', DATE_FORMAT(dateTime, '%d/%m/%Y - %H:%i:%s') AS 'dateTime', DATE_FORMAT(al.dateTime, '%Y-%m-%d') AS 'date', DATE_FORMAT(al.dateTime, '%H:%i:%s') AS 'time', al.action, al.sql
				FROM			".DB_TBL_PREFIX."actionlogs AS al
				LEFT JOIN 		".DB_TBL_PREFIX."users AS u ON u.userID = al.userID
				LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.userID = al.userID AND u.isExternal = 0
				WHERE			1=1
				AND				logID = ".$logID."
				LIMIT			1
			";
			
			$stmt = $this->DB->rawQuery( $sql );
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
		function getLogisticLogs($extraSQL = NULL, $orderBySQL = 'ORDER BY al.userID, al.dateTime ASC')
		{
			$sql = 
			"
				SELECT 			DATE_FORMAT(al.dateTime, '%Y-%m-%d') AS 'date', DATE_FORMAT(al.dateTime, '%H:%i:%s') AS 'time', IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS 'userName', DATE_FORMAT(p.date,'%d/%m/%Y') AS 'plannerDate', wd.workID, w.workRef, IF(p.workDetailID > 0, wt.name, p.workType) AS 'workTypeName', w.venue, IF(p.workDetailID > 0, w.location, p.location) AS 'location', IF(p.workDetailID > 0, AES_DECRYPT(c.name, '".ENCRYPTION_KEY."'), p.custName) AS 'custName', IF(p.workDetailID > 0, wd.jobDescription, p.jobDescription) AS 'jobDescription', t.employeeNames, vt.vehicleNames, tt.trailerNames, IF(LOCATE('isScheduled = 1', al.sql) > 0, 'Scheduled', 'Pending') AS 'plannerStatus', p.isDeleted
				FROM			".DB_TBL_PREFIX."actionlogs AS al 
				LEFT JOIN 		".DB_TBL_PREFIX."users AS u ON u.userID = al.userID
				LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.userID = al.userID AND u.isExternal = 0
				LEFT JOIN 		".DB_TBL_PREFIX."planners AS p ON p.workPlannerID = TRIM(REPLACE(SUBSTRING_INDEX(SUBSTRING_INDEX(al.sql, 'workPlannerID = ', - 1), 'LIMIT 1', 1), 'LIMIT 	\t\t1', ''))
				LEFT JOIN 		(SELECT 		t.workPlannerID, GROUP_CONCAT(t.employeeID SEPARATOR ', ') AS employeeIDs, GROUP_CONCAT(CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."')) SEPARATOR ', ') AS employeeNames 
								FROM 			".DB_TBL_PREFIX."timesheets AS t 
								LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.employeeID = t.employeeID 
								WHERE 			t.isDeleted = 0
								GROUP BY 		t.workPlannerID) AS t ON t.workPlannerID = p.workPlannerID 
				LEFT JOIN 		(SELECT 		vt.workPlannerID, GROUP_CONCAT(vt.vehicleID SEPARATOR ', ') AS vehicleIDs, GROUP_CONCAT(v.regNum SEPARATOR ', ') AS vehicleNames 
								FROM 			".DB_TBL_PREFIX."vehicletimesheets AS vt 
								LEFT JOIN 		".DB_TBL_PREFIX."vehicles AS v ON v.vehicleID = vt.vehicleID
								WHERE 			vt.isDeleted = 0 
								GROUP BY 		vt.workPlannerID ) AS vt ON vt.workPlannerID = p.workPlannerID 
				LEFT JOIN 		(SELECT 		tt.workPlannerID, GROUP_CONCAT(t.trailerRef SEPARATOR ', ') AS trailerNames 
								FROM 			".DB_TBL_PREFIX."trailertimesheets AS tt 
								LEFT JOIN 		".DB_TBL_PREFIX."trailers AS t ON t.trailerID = tt.trailerID 
								WHERE 			tt.isDeleted = 0
								GROUP BY 		tt.workPlannerID ) AS tt ON tt.workPlannerID = p.workPlannerID
				LEFT JOIN 		".DB_TBL_PREFIX."workdetails AS wd ON wd.workDetailID = p.workDetailID
				LEFT JOIN 		".DB_TBL_PREFIX."works AS w ON w.workID = wd.workID
				LEFT JOIN 		".DB_TBL_PREFIX."worktypes AS wt ON wt.workTypeID = w.workTypeID
				LEFT JOIN 		".DB_TBL_PREFIX."jobrequirements AS jr ON jr.jobReqID = wd.jobReqID
				LEFT JOIN 		".DB_TBL_PREFIX."customers AS c ON c.customerID = w.customerID
				WHERE 			al.action LIKE '% has updated work planner %'
				".$extraSQL."
				AND 			p.isDeleted = 0
				".$orderBySQL."
			";
			$stmt = $this->DB->rawQuery( $sql );
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
        function countActivityLogs($extraSQL)
		{
			$sql = 
			"
				SELECT 				COUNT(*) AS 'totalActivity'
				FROM 				".DB_TBL_PREFIX."actionlogs AS al
				WHERE 				1=1
				".$extraSQL."
			";
					  
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt[0]['totalActivity'];
			} else {
				return 0;
			}
		}
        
		function countLast6Months()
		{
			$sql = 
			"
				SELECT			COUNT(*) AS 'number'
				FROM			".DB_TBL_PREFIX."actionlogs AS al
				WHERE			1=1
				AND al.dateTime > DATE_SUB(NOW(), INTERVAL 3 MONTH)
			";
			
			$stmt = $this->DB->rawQuery( $sql );
			if(count($stmt) > 0) {
				return $stmt[0]['number'];
			} else {
				return 0;
			}
		}
		
		function sqlTiming()
		{
			list($usec, $sec) = explode(' ', microtime());
			return ((float)$usec + (float)$sec);
		}
		
		function insert($action = NULL, $data = NULL, $return_id = false, $relTypeID = 0, $relationID = 0)
		{
			$logData = json_encode($data, JSON_UNESCAPED_SLASHES);
			$logData = preg_replace('/\\\\\'/', '\\\'', $logData);
			$arrLogData = array(
				'relTypeID' => $relTypeID,
				'relationID' => $relationID,
				'userID' => ((isset($_SESSION['userID']) && $_SESSION['userID'] > 0) ? $_SESSION['userID'] : '-1'),
				'dateTime' => date('Y-m-d H:i:s'),
				'action' => $action,
				'sql' => $logData
			);
			
			$id = $this->DB->insert(DB_NAME.'.'.DB_TBL_PREFIX.'actionlogs', $arrLogData);
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
	}
	
?>