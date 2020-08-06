<?php

	class Auth
	{
		private $salt = '_c5L-iNdU5tRiAl!636';
		private $custPortalSalt = '_tR/\fF1c-MaNAgEm3Nt!363';
		
		function __construct() {
			//global $oTemplate;
		}

		function getAuthSalt()
		{
			return $this->salt;
		}
		
		function getCustPortalSalt()
		{
			return $this->custPortalSalt;
		}
		
		function validateUserReset($email)
		{
			global $oDatabase;
			$this->DB = $oDatabase;
			
			$sql = "
				SELECT			u.userID, e.employeeID, u.username, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS 'fullName', IF(u.isExternal = 1, u.forename, AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."')) AS 'forename', IF(u.isExternal = 1, u.surname, AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."')) AS 'surname', IF(u.isExternal = 1, u.email, AES_DECRYPT(e.workEmail, '".ENCRYPTION_KEY."')) AS 'email', IF(u.isExternal = 1, u.extNum, e.extNum) AS 'extNum', IF(u.isExternal = 1, u.position, AES_DECRYPT(e.jobTitle, '".ENCRYPTION_KEY."')) AS 'position', IF(u.isExternal = 1, u.employeeTypeID, e.employeeTypeID) AS 'employeeTypeID', IF(u.isExternal = 1, u.depotID, e.depotID) AS 'depotID', IF(u.isExternal = 1, u.departmentID, e.departmentID) AS 'departmentID', IF(u.isExternal = 1, AES_DECRYPT(d2.email, '".ENCRYPTION_KEY."'), AES_DECRYPT(d1.email, '".ENCRYPTION_KEY."')) AS 'departmentEmail', u.signature, u.isExternal, u.hasMultiCompanyAccess
				FROM 			".DB_TBL_PREFIX."users AS u
				LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.userID = u.userID
				LEFT JOIN 		".DB_TBL_PREFIX."departments AS d1 ON d1.departmentID = e.departmentID AND u.isExternal = 0
				LEFT JOIN 		".DB_TBL_PREFIX."departments AS d2 ON d2.departmentID = u.departmentID AND u.isExternal = 1
				WHERE 			1=1
				AND 			IF(u.isExternal = 1, u.email, AES_DECRYPT(e.workEmail, '".ENCRYPTION_KEY."')) = '".$email."'
				AND 			u.isDeleted = 0
				
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				return $stmt;
			} else {
				return false;
			}
		}
		
		function validateLogin($user,$pass)
		{
			global $oDatabase;
			$this->DB = $oDatabase;
			
			$sql = "
				SELECT			u.userID, e.employeeID, u.username, u.salt, u.password, IF(u.isExternal = 1, CONCAT(u.forename, ' ', u.surname), CONCAT(AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."'), ' ', AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."'))) AS 'fullName', IF(u.isExternal = 1, u.forename, AES_DECRYPT(e.forename, '".ENCRYPTION_KEY."')) AS 'forename', IF(u.isExternal = 1, u.surname, AES_DECRYPT(e.surname, '".ENCRYPTION_KEY."')) AS 'surname', IF(u.isExternal = 1, u.email, AES_DECRYPT(e.workEmail, '".ENCRYPTION_KEY."')) AS 'email', IF(u.isExternal = 1, u.extNum, e.extNum) AS 'extNum', IF(u.isExternal = 1, u.position, AES_DECRYPT(e.jobTitle, '".ENCRYPTION_KEY."')) AS 'position', IF(u.isExternal = 1, u.employeeTypeID, e.employeeTypeID) AS 'employeeTypeID', IF(u.isExternal = 1, u.depotID, e.depotID) AS 'depotID', IF(u.isExternal = 1, u.companyID, e.companyID) AS 'companyID',  IF(u.isExternal = 1, u.departmentID, e.departmentID) AS 'departmentID', IF(u.isExternal = 1, AES_DECRYPT(d2.email, '".ENCRYPTION_KEY."'), AES_DECRYPT(d1.email, '".ENCRYPTION_KEY."')) AS 'departmentEmail', u.signature, u.groupPermissions, u.isExternal, u.hasMultiCompanyAccess, AES_DECRYPT(u.securityPhone, '".ENCRYPTION_KEY."') AS 'securityPhone'
				FROM 			".DB_TBL_PREFIX."users AS u
				LEFT JOIN 		".DB_TBL_PREFIX."employees AS e ON e.userID = u.userID
				LEFT JOIN 		".DB_TBL_PREFIX."departments AS d1 ON d1.departmentID = e.departmentID AND u.isExternal = 0
				LEFT JOIN 		".DB_TBL_PREFIX."departments AS d2 ON d2.departmentID = u.departmentID AND u.isExternal = 1
				WHERE 			1=1
				AND 			u.username = '".$user."'
				AND 			u.isDeleted = 0
				
			";
			$stmt = $this->DB->rawQuery($sql);
			if(count($stmt) > 0) {
				if(password_verify( $pass.$stmt[0]['salt'].$this->custPortalSalt, $stmt[0]['password']) == true) {
					return $stmt;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		
		function checkLoginStatus($permissionName = '', $action = '')
		{
			global $oTemplate;
			if(isset($_SESSION['loggedIn'])) {
				if(time() > $_SESSION['lastActiveState'] + SESSION_TIMEOUT) {
					$oSession = new Session();
					$rstDelSession = $oSession->delete($_SESSION['sessionID']);
					session_destroy();
					if($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443){
						if(isset($_SERVER['QUERY_STRING']) && (!empty($_SERVER['QUERY_STRING'])))
						{
							$deferURL = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
						} else {
							$deferURL = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
						}
					} else {
						if(isset($_SERVER['QUERY_STRING']) && (!empty($_SERVER['QUERY_STRING']))) {
							$deferURL = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
						} else {
							$deferURL = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
						}
					}
					
					$encodeDeferURL = base64_encode($deferURL);
					$oTemplate->setAlert('Your Active Session has timedout due to exceeding '.floor(SESSION_TIMEOUT / 60).' mins of inactivity!','error');
					if($deferURL != ADMIN_URL.'login.php') {
						$oTemplate->redirect(SITE_URL.'pages/login.php?defer='.$encodeDeferURL);
					} else {
						$oTemplate->redirect(SITE_URL.'pages/login.php');
					}
				} else {
					$_SESSION['lastActiveState'] = time();
					//update when session was last active? 
					if($this->validateSession() == true ) {
						if($permissionName != '' && $action != '') {
							return $this->checkPermissionAccess($permissionName, $action, 0, true);
						} else {
							return true;
						}
					} else {
						//no valid session log them out 
						
						if($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443){
							if(isset($_SERVER['QUERY_STRING']) && (!empty($_SERVER['QUERY_STRING'])))
							{
								$deferURL = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
							} else {
								$deferURL = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
							}
						} else {
							if(isset($_SERVER['QUERY_STRING']) && (!empty($_SERVER['QUERY_STRING']))) {
								$deferURL = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
							} else {
								$deferURL = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
							}
						}

						$encodeDeferURL = base64_encode($deferURL);

						if($_SESSION['sessionID']) {
							$oSession = new Session();
							$rstDelSession = $oSession->delete($_SESSION['sessionID']);
						}

						$_SESSION = array();
						session_unset();
						session_destroy();

						if($deferURL != SITE_URL.'pages/login.php' && $deferURL != SITE_URL.'index.php') {
							$oTemplate->redirect(SITE_URL.'pages/login.php?defer='.$encodeDeferURL);
						} else {
							$oTemplate->redirect(SITE_URL.'pages/login.php');
						}
						//$oTemplate->redirect(SITE_URL.'pages/login.php');
						exit();
					}
				}
			} elseif(isset($_SESSION['pendingVerification'])){
				
				if(time() > $_SESSION['lastActiveState'] + 300) {
					$oSession = new Session();
					$rstDelSession = $oSession->delete($_SESSION['sessionID']);
					session_destroy();
					if($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443){
						if(isset($_SERVER['QUERY_STRING']) && (!empty($_SERVER['QUERY_STRING']))){
							$deferURL = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
						} else {
							$deferURL = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
						}
					} else {
						if(isset($_SERVER['QUERY_STRING']) && (!empty($_SERVER['QUERY_STRING']))) {
							$deferURL = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
						} else {
							$deferURL = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
						}
					}
					
					$encodeDeferURL = base64_encode($deferURL);
					$oTemplate->setAlert('Your verification code has expired!','error');
					if($deferURL != ADMIN_URL.'login.php') {
						$oTemplate->redirect(SITE_URL.'pages/login.php?defer='.$encodeDeferURL);
					} else {
						$oTemplate->redirect(SITE_URL.'pages/login.php');
					}
				} else {
					$oTemplate->redirect(SITE_URL.'pages/verify.php');	
					exit();	
				}
			} else {
				
				if($_SERVER['SERVER_PORT'] == 80 || $_SERVER['SERVER_PORT'] == 443){
					if(isset($_SERVER['QUERY_STRING']) && (!empty($_SERVER['QUERY_STRING'])))
					{
						$deferURL = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
					} else {
						$deferURL = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
					}
				} else {
					if(isset($_SERVER['QUERY_STRING']) && (!empty($_SERVER['QUERY_STRING']))) {
						$deferURL = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];
					} else {
						$deferURL = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['PHP_SELF'];
					}
				}
				
				$encodeDeferURL = base64_encode($deferURL);
				
				if($_SESSION['sessionID']) {
					$oSession = new Session();
					$rstDelSession = $oSession->delete($_SESSION['sessionID']);
				}
				
				$_SESSION = array();
				session_unset();
				session_destroy();
				
				if($deferURL != SITE_URL.'pages/login.php' && $deferURL != SITE_URL.'index.php') {
					$oTemplate->redirect(SITE_URL.'pages/login.php?defer='.$encodeDeferURL);
				} else {
					$oTemplate->redirect(SITE_URL.'pages/login.php');
				}
				
				//$oTemplate->redirect(SITE_URL.'pages/login.php');
				exit();
			}
		}
		
		function checkPermissionAccess($permissionName, $action, $companyID = 0, $redirect = false) {
			global $oTemplate;
			$oUserPermission = new UserPermission();
			$checkPermission = $oUserPermission->check("AND up.userID = ".$_SESSION['userID']." AND p.name = '".$permissionName."' AND up.".$action." = 1 ".(($companyID > 0) ? "AND up.companyID = ".$companyID : ""));
			if($checkPermission == true) {
				return true;
			} else {
				if($redirect == true) {
					$oTemplate->setMetaData('Access Denied!','pageTitle');
					$oTemplate->load(ADMIN_VIEWS.'v_403.php');
					exit();
				} else {
					return false;
				}
			}
			$_SESSION['lastActiveState'] = time();
		}
        
        function checkEmpEventPermissionAccess($action, $companyID = 0) {
			global $oTemplate;
			$oUserPermission = new UserPermission();
			$checkPermission = $oUserPermission->check("AND up.userID = ".$_SESSION['userID']." AND p.name LIKE '%adminHREmployeeEvents%' AND p.name != 'adminHREmployeeEventsStatistics' AND up.".$action." = 1 ".(($companyID > 0) ? "AND up.companyID = ".$companyID : ""));
			if($checkPermission == true) {
				return true;
			} else {
                return false;
			}
			$_SESSION['lastActiveState'] = time();
		}
		
		function checkSectorPermissionAccess($sectorName) {
			global $oTemplate;
			$oUserPermission = new UserPermission();
			$checkSectorPermission = $oUserPermission->getAll("AND up.userID = ".$_SESSION['userID']." AND p.sector = '".$sectorName."' AND (up.index = 1 OR up.add = 1 OR up.edit = 1 OR up.view = 1 OR up.delete = 1)");
			if($checkSectorPermission == true) {
				return true;
			} else {
				return false;
			}
		}
		
		function validateSession() {
			// need to check to see if the userID is in the sessions more than once with diffrent IP Addresses.
			global $oTemplate;
			$oSession = new Session();
			$chkCurrentSession = $oSession->getAll("AND userID = ".$_SESSION['userID']." AND userSessionID = '".$_SESSION['sessionID']."' ");
			if($chkCurrentSession) { // current session is good now check for others
				$chkAllSessions = $oSession->getAll("AND userID = ".$_SESSION['userID']." AND userSessionID != '".$_SESSION['sessionID']."' ");
				if($chkAllSessions) {
					// multiple sessions identified - log all others out of the system !
					foreach($chkAllSessions as $dbSession) {
						if($dbSession['userSessionID'] != '') {
							
							// hyjack the invalid sessions 
							// destroy them
							session_id($dbSession['userSessionID']);
							session_start();
							session_destroy();
							$rstDelSession = $oSession->delete($dbSession['userSessionID']);
						}
					}
					
					//restore the session - chkCurrentSession[0]['userSessionID'] - the current users session.
					session_id($chkCurrentSession[0]['userSessionID']);
					session_start();
					return true;
					
				} else {
					//no duplicate sessions found
					return true;
				}
			} else {
				// no session exists - log them out
				return false;
			}
			
		}
		
		function logout()
		{
			global $oTemplate;
			$oSession = new Session();
			$rstDelSession = $oSession->delete($_SESSION['sessionID']);
			//need to remove session from the DB using the userSessionID
			session_destroy();
		}
	}

?>