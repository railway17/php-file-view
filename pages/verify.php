<?php

	require_once('../library/config.php');

	if (isset($_POST['login']) || isset($_POST['resendCode'])) {
		
		if(isset($_SESSION['loggedIn'])){
			$_SESSION['lastActiveState'] = time();
			if($_SESSION['departmentID'] == 1) {
				$oTemplate->redirect(ADMIN_URL.'works/index.php?worktypeid=1');
			} elseif($_SESSION['departmentID'] == 2) {
				$oTemplate->redirect(ADMIN_URL.'works/index.php?worktypeid=2');
			} elseif($_SESSION['departmentID'] == 5) {
				$oTemplate->redirect(ADMIN_URL.'logistics/planners/index.php');
			} else {
				$oTemplate->redirect(ADMIN_URL.'dashboard/index.php');	
			}
			exit();
		} elseif(isset($_SESSION['pendingVerification'])) {

			if(isset($_POST['login'])){
				if(time() > $_SESSION['lastActiveState'] + 300) {
					$oSession = new Session();
					$rstDelSession = $oSession->delete($_SESSION['sessionID']);
					session_destroy();
					$oTemplate->setAlert('Your verification code has expired!', 'danger');
					$oTemplate->redirect(PUBLIC_URL.'login.php');	
					exit();	
				} else {
					$oAuth = new Auth();
					if ($_POST['verificationCode'] == '') {
						if ($_POST['verificationCode'] == '') { 
							$errorMsg[] = 'Verification Code';
						}
						if(!empty($errorMsg)) {
							$countErr = count($errorMsg);
							$errorMsg = implode(", ",$errorMsg);
							if($countErr > 0) {
								$oTemplate->setAlert('You must enter your '.$errorMsg.' in order to log into the system','danger');
							}
							$oTemplate->load(SITE_VIEWS.'v_verify.php');
						}
					} else {	
						
						$oLoginAttempt = new LoginAttempt();
						//check for failed attempts before allowing it to process - brutal force
						$chkLoginAttempts = $oLoginAttempt->getAll("AND username = '".$_SESSION['username']."' AND remoteIP = '".$_SERVER['REMOTE_ADDR']."' AND attemptDate >= '".date('Y-m-d H:i:s')."' - INTERVAL 15 MINUTE");
						if($chkLoginAttempts ) {
							$failedAttempts = count($chkLoginAttempts);
						} else {
							$failedAttempts = 0;
						}
						
						if($failedAttempts < 6) {

							if($_POST['verificationCode'] == $_SESSION['verificationCode']){

								$_SESSION['lastActiveState'] = time();
								$_SESSION['loggedIn'] = true;
								unset($_SESSION['pendingVerification']);
								unset($_SESSION['verificationCode']);
								
								$oActionLog->insert($_SESSION['fullname'].' has logged into the system on '.gethostbyaddr($_SERVER['REMOTE_ADDR']), '');

								if($_SESSION['departmentID'] == 1) {
									$oTemplate->redirect(ADMIN_URL.'works/index.php?worktypeid=1');
								} elseif($_SESSION['departmentID'] == 2) {
									$oTemplate->redirect(ADMIN_URL.'works/index.php?worktypeid=2');
								} elseif($_SESSION['departmentID'] == 5) {
									$oTemplate->redirect(ADMIN_URL.'logistics/planners/index.php');
								} else {
									$oTemplate->redirect(ADMIN_URL.'dashboard/index.php');	
								}	
							} else {

								$attemptData = array(
									'username' => $_SESSION['username'],
									'userAgent' => $_SERVER['HTTP_USER_AGENT'], 
									'remoteIP' => $_SERVER['REMOTE_ADDR'],
									'attemptDate' => date('Y-m-d H:i:s')
								);
								$loginAttemptID = $oLoginAttempt->insert($attemptData, true);

								$oTemplate->setAlert('You have entered an incorrect code!' , 'danger');
								$oTemplate->load(SITE_VIEWS.'v_verify.php');
								exit();	
							}
						} else {
							$oSession = new Session();
							$rstDelSession = $oSession->delete($_SESSION['sessionID']);
							session_destroy();
							$oTemplate->setAlert('Too many invalid logins have been attempted please try again in 15 minutes', 'danger');
							$oTemplate->redirect(PUBLIC_URL.'login.php');	
							exit();	
						}
					} 	
				}	
			} elseif(isset($_POST['resendCode'])){
				$oTextMagic = new TextMagic();
				$securityPhone = substr_replace($_SESSION['securityPhone'], '+44', 0, 1);
				$verifyCode = getRandomNumbers(6);
				$sendSMSMessage = $oTextMagic->sendSMSMessage($securityPhone,$verifyCode);
				if($sendSMSMessage['id']){	
					$_SESSION['verificationCode'] = $verifyCode;
					$oTemplate->setAlert('A new code has been sent to your mobile!', 'success');	
					$oTemplate->load(SITE_VIEWS.'v_verify.php');	
					exit();
				} else {
					$oTemplate->setAlert($sendSMSMessage['message'], 'danger');	
					$oTemplate->load(SITE_VIEWS.'v_verify.php');	
					exit();	
				}
			}
		}
	} else {
		$oTemplate->load(SITE_VIEWS.'v_verify.php');
		exit();
	}
?>