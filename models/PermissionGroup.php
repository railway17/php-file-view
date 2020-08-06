<?php

	class PermissionGroup
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
				SELECT			*
				FROM 			".DB_TBL_PREFIX."permissiongroups
				WHERE			1=1
				".$extraSQL."
                ORDER BY		groupID ASC
			";

			$stmt = $this->DB->rawQuery($sql);
			if (count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
        }
    }
        
?>