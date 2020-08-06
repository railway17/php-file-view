<?php

	require_once('../library/config.php');
    require_once(ASSETS_PHP_ROOT.'phpmailer/'.PHP_MAILER_VERS.'/class.phpmailer.php');
	require_once(ASSETS_PHP_ROOT.'mobileDetect/mobile_Detect.php');

	$detect = new Mobile_Detect;
	if ($detect->isMobile()) {
		$oTemplate->setData('isMobile', true);
	}

	$footerCode = '
	<script type="text/javascript">
		$(document).ready(function(){							
			/*$("#checkLogin").on("init.field.fv", function(e, data) {
                // data.fv      --> The FormValidation instance
                // data.field   --> The field name
                // data.element --> The field element

                var $icon      = data.element.data("fv.icon"),
                    options    = data.fv.getOptions(),                      // Entire options
                    validators = data.fv.getOptions(data.field).validators; // The field validators

                if (validators.notEmpty && options.icon && options.icon.required) {
                    // The field uses notEmpty validator
                    // Add required icon
                    $icon.addClass(options.icon.required).show();
                }
            }).formValidation({
                framework: "bootstrap",
                excluded: ":disabled",
                icon: {
                    required: "glyphicon glyphicon-asterisk",
                    valid: "glyphicon glyphicon-ok",
                    invalid: "glyphicon glyphicon-remove",
                    validating: "glyphicon glyphicon-refresh"
                },
                fields: {
					username: {
						validators: {
							notEmpty: {
								message: "The username field is required"
							},
							stringLength: {
								min: 5,
								max: 30,
								message: "The username must be more than 5 and less than 30 characters long"
							},
							regexp: {
								enabled: true,
								regexp: "^[a-zA-Z0-9_\.]+$",
								message: "The username can only consist of alphabetical, number, dot and underscore"
							}
						}
					},
					password: {
						validators: {
							notEmpty: {
								message: "The password field is required"
							}
						}
					}
				}
			}).on("err.field.fv", function(e, data) {
                if (data.fv.getInvalidFields().length > 0) {
                    $("#checkLogin button#btnSave").prop("disabled", true);
                }
            }).on("success.field.fv", function(e, data) {
                if (data.fv.getInvalidFields().length <= 0) {
                    $("#checkLogin button#btnSave").prop("disabled", false);
                }
            });*/
			
			$("#forgottenPassword").on("click", function() {
				$("#loginDetails").hide();
				$("#forgottenDetails").show();
			});
			
			$("#returnToLogin").on("click", function() {
				$("#loginDetails").show();
				$("#forgottenDetails").hide();
			});
			
			$("#checkLogin input").keydown(function(e) {
				var key = e.which ? e.which : e.keyCode;
				if(key === 13) {
					$("#btnLogin").prop("disabled", "disabled");
					$("#checkLogin").append("<input type=\'hidden\' name=\'login\' value=\'Login\'>");
					$("#checkLogin").submit();
					return false;
				}
			});
		});
	</script>';
	
	$oTemplate->setInclude($footerCode,'footerCode');
	$oTemplate->setData('updateSecurityPhone', false);
	$deferURL = isset($_GET['defer']) ? $_GET['defer'] : NULL;
		
	if (isset($_POST['login'])) {
		$oAuth = new Auth();
		$oTemplate->setData('inpUser', $_POST['username']);
		$oTemplate->setData('inpPass', $_POST['password']);
		$oTemplate->setData('inpSecurityPhone', $_POST['securityPhone']);
		if ($_POST['username'] == '' || $_POST['password'] == '') {
			if ($_POST['username'] == '') { 
				$oTemplate->setData('errorUsername', '* required field!');
				$errorMsg[] = 'Username';
			}
			if ($_POST['password'] == '') {
				$oTemplate->setData('errorPassword', '* required field!');
				$errorMsg[] = 'Password';
			}
			if(!empty($errorMsg)) {
				$countErr = count($errorMsg);
				$errorMsg = implode(", ",$errorMsg);
				if($countErr > 0) {
					$oTemplate->setAlert('You must enter your '.$errorMsg.' inorder to login into the system','danger');
				}
				$oTemplate->load(SITE_VIEWS.'v_login.php');
			}
		} else {
			
			$oLoginAttempt = new LoginAttempt();
			//check for failed attempts before allowing it to process - brutal force
			$chkLoginAttempts = $oLoginAttempt->getAll("AND username = '".$oTemplate->getData('inpUser')."' AND remoteIP = '".$_SERVER['REMOTE_ADDR']."' AND attemptDate >= '".date('Y-m-d H:i:s')."' - INTERVAL 15 MINUTE");
			if($chkLoginAttempts ) {
				$failedAttempts = count($chkLoginAttempts);
			} else {
				$failedAttempts = 0;
			}
			if($failedAttempts < 3) {
				if($_POST['securityPhone'] != '' && $_POST['securityPhone'] != NULL){
					$securityData = array('securityPhone'=>DB_ENCRYPT($_POST['securityPhone']));
					$oUser = new User();
					$rstUser = $oUser->update($securityData, $_SESSION['userID']);
				}
				$dbUser = $oAuth->validateLogin($oTemplate->getData('inpUser'),$oTemplate->getData('inpPass'));

				if($dbUser == false) {
					// store failed attempt
					$attemptData = array(
						'username' => $oTemplate->getData('inpUser'),
						'userAgent' => $_SERVER['HTTP_USER_AGENT'], 
						'remoteIP' => $_SERVER['REMOTE_ADDR'],
						'hostName' =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
						'attemptDate' => date('Y-m-d H:i:s')
					);
					$loginAttemptID = $oLoginAttempt->insert($attemptData, true);
					$oTemplate->setAlert('Invalid username or password!', 'danger');
					$oTemplate->load(SITE_VIEWS.'v_login.php');
					exit();
				} else {

					$oSession = new Session();
                    $_SESSION['sessionID'] = session_id();
                    $_SESSION['userID'] = $dbUser[0]['userID'];
                    $_SESSION['employeeID'] = $dbUser[0]['employeeID'];
                    $_SESSION['fullname'] = $dbUser[0]['forename'].' '.$dbUser[0]['surname'];
                    $_SESSION['forename'] = $dbUser[0]['forename'];
                    $_SESSION['surname'] = $dbUser[0]['surname'];
                    $_SESSION['employeeTypeID'] = $dbUser[0]['employeeTypeID'];
                    $_SESSION['username'] = $dbUser[0]['username'];
                    $_SESSION['email'] = $dbUser[0]['email'];
                    $_SESSION['securityPhone'] = $dbUser[0]['securityPhone'];
                    $_SESSION['position'] = $dbUser[0]['position'];
                    $_SESSION['departmentID'] = $dbUser[0]['departmentID'];
                    $_SESSION['departmentEmail'] = $dbUser[0]['departmentEmail'];
                    $_SESSION['hasMultiCompanyAccess'] = $dbUser[0]['hasMultiCompanyAccess'];
                    $_SESSION['companyID'] = $dbUser[0]['companyID'];
                    $_SESSION['depotID'] = $dbUser[0]['depotID'];
                    $_SESSION['groupPermissions'] = $dbUser[0]['groupPermissions'];
                    $_SESSION['signature'] = $dbUser[0]['signature'];
                    $_SESSION['lastActiveState'] = time();
                    
                    if($dbUser[0]['companyID'] > 0){
                        $oCompany = new Company();
                        $favouredCustomers = $oCompany->getFavouredCustomers($dbUser[0]['companyID']);
                        $_SESSION['favouredESCustomerIDs'] = json_decode($favouredCustomers[0]['favouredESCustomerIDs']);
                        $_SESSION['favouredTMCustomerIDs'] = json_decode($favouredCustomers[0]['favouredTMCustomerIDs']);
                        $_SESSION['favouredMECustomerIDs'] = json_decode($favouredCustomers[0]['favouredMECustomerIDs']);
                        $_SESSION['favouredCPCustomerIDs'] = json_decode($favouredCustomers[0]['favouredCPCustomerIDs']);
                        $_SESSION['favouredSRNCustomerIDs'] = json_decode($favouredCustomers[0]['favouredSRNCustomerIDs']);
                    }
					
					$ipAddress = ip2long($_SERVER['REMOTE_ADDR']);
                    
                    if (in_array($ipAddress, range(ip2long('192.168.0.0'),ip2long('192.168.255.255')))) {
                        $inRange = true;
                    } elseif (in_array($ipAddress, range(ip2long('172.16.0.0'),ip2long('172.31.255.255')))) {
                        $inRange = true;    
                    } elseif (in_array($ipAddress, range(ip2long('10.100.100.0'),ip2long('10.100.100.255')))) {
                        $inRange = true;     
                    } elseif (in_array($ipAddress, range(ip2long('10.82.234.0'),ip2long('10.82.234.255')))) {
                        $inRange = true;     
                    } elseif (in_array($ipAddress, range(ip2long('185.221.147.0'),ip2long('185.221.147.255')))) {
                        $inRange = true;     
                    } else {
                        $inRange = false;
                    }

					if($inRange == false){
						
                        $oTextMagic = new TextMagic();
						$securityPhone = substr_replace($dbUser[0]['securityPhone'], '+44', 0, 1);
						$verifyCode = getRandomNumbers(6);
						$sendSMSMessage = $oTextMagic->sendSMSMessage($securityPhone,$verifyCode);
						
                        if($sendSMSMessage['id']){

							$_SESSION['pendingVerification'] = true;
							$_SESSION['verificationCode'] = $verifyCode;	
							
							$sessionData = array(
								'userID' => $dbUser[0]['userID'], 
								'username' => $dbUser[0]['username'], 
								'userSessionID' => $_SESSION['sessionID'], 
								'userAgent' => $_SERVER['HTTP_USER_AGENT'], 
								'remoteIP' => $_SERVER['REMOTE_ADDR'],
								'hostName' =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
								'createdDate' => date('Y-m-d H:i:s')
							);
							$sessionID = $oSession->insert($sessionData, true);
							$oTemplate->redirect(PUBLIC_URL.'verify.php');
						} else {
							$oTemplate->setData('updateSecurityPhone', true);
							$_SESSION['userID'] = $dbUser[0]['userID'];
							$oTemplate->setAlert($sendSMSMessage['message'], 'info');	
							$oTemplate->load(SITE_VIEWS.'v_login.php');	
						}

					} else {
						//check to see if another user is already logged in with the same credentials.

						//$chkSessionExists = $oSession->getAll("AND userID = ".$dbUser[0]['userID']." AND remoteIP != '".$_SERVER['REMOTE_ADDR']."' ");

						//if($chkSessionExists == false) {

							$_SESSION['loggedIn'] = true;
                        
							$oActionLog->insert($_SESSION['fullname'].' has logged into the system on '.gethostbyaddr($_SERVER['REMOTE_ADDR']), '');
							//need to store session with IP

							$sessionData = array(
								'userID' => $dbUser[0]['userID'], 
								'username' => $dbUser[0]['username'], 
								'userSessionID' => $_SESSION['sessionID'], 
								'userAgent' => $_SERVER['HTTP_USER_AGENT'], 
								'remoteIP' => $_SERVER['REMOTE_ADDR'],
								'hostName' =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
								'createdDate' => date('Y-m-d H:i:s')
							);
							$sessionID = $oSession->insert($sessionData, true);

							if($deferURL != '') {
								$decodeDeferURL = base64_decode($deferURL);
								$oTemplate->redirect($decodeDeferURL);
							} else {
								if($dbUser[0]['departmentID'] == 1) {
									$oTemplate->redirect(ADMIN_URL.'works/index.php?worktypeid=1');
								} elseif($dbUser[0]['departmentID'] == 2) {
									$oTemplate->redirect(ADMIN_URL.'works/index.php?worktypeid=2');
								} elseif($dbUser[0]['departmentID'] == 5) {
									$oTemplate->redirect(ADMIN_URL.'logistics/planners/index.php');
								} else {
									$oTemplate->redirect(ADMIN_URL.'dashboard/index.php');	
								}
							}
						/*} else {
							// existing session identified from another computer - check to see if this session is still active. 
							session_id($chkSessionExists[0]['userSessionID']);
							session_start();

							var_dump(session_status());
							if(session_status() === PHP_SESSION_ACTIVE) {
								echo 'active';
							} else {
								echo 'not active';
							}
							exit;
							$oTemplate->setAlert('Another user is already logged in using these credentials, if that user is not you please contact the system admin.', 'danger');
							$oTemplate->load(SITE_VIEWS.'v_login.php');
							exit();
						}*/
					}
				}
			} else {
				$oTemplate->setAlert('Too many invalid logins have been attempted please try again in 15 minutes', 'danger');
				$oTemplate->load(SITE_VIEWS.'v_login.php');
				exit();
			}
		}
	} elseif(isset($_POST['resetPassword'])) {
		$Auth = new Auth();
		$oTemplate->setData('inpEmail', $_POST['email']);
		if ($_POST['email'] == '' ) {
			if ($_POST['email'] == '') { 
				$oTemplate->setData('errorEmail', '* required field!');
				$errorMsg[] = 'Email';
			}
			if(!empty($errorMsg)) {
				$countErr = count($errorMsg);
				$errorMsg = implode(", ",$errorMsg);
				if($countErr > 0) {
					$oTemplate->setAlert('You must enter your '.$errorMsg.' in order to reset your password','danger');
				}
				$oTemplate->load(SITE_VIEWS.'v_login.php');
			}
		} else {
			$dbUser = $oAuth->validateUserReset($oTemplate->getData('inpEmail'));
			if($dbUser == false) {
				$oTemplate->setAlert('Invalid email!', 'danger');
				$oTemplate->load(SITE_VIEWS.'v_login.php');
				exit();
			} else {
				$emailGreeting = getEmailGreeting();
				//new one -> we must now create a code for them to input into a secondary page with a link. 
				
				$oPasswordReset = new PasswordReset();
				
				//get code.
				$authCode = getRandomNumbers(6);
				
				//save the reset record
				$resetData = array(
					'email' => $oTemplate->getData('inpEmail'),
					'authCode' => $authCode,
					'userID' => $dbUser[0]['userID'],
					'userAgent' => $_SERVER['HTTP_USER_AGENT'], 
					'remoteIP' => $_SERVER['REMOTE_ADDR'],
					'hostName' =>  gethostbyaddr($_SERVER['REMOTE_ADDR']),
					'createdDate' => date('Y-m-d H:i:s')
				);
				$resetID = $oPasswordReset->insert($resetData, true);
				if($resetID > 0) {
					//send email with link to new page and code

					$emailBody = $emailGreeting.' '.$dbUser[0]['forename'].',<br><br>Please see the reset code below. Copy it and follow the link to reset your password.<br><br><a href="'.PUBLIC_URL.'password-reset.php?authcode='.$authCode.'"><strong>'.$authCode.'</strong></a></p>Please be aware that this code will expire in 30 minutes.';
                    
                    $recipients[] = array (
                        'name' => $dbUser[0]['fullname'],
                        'email' => $dbUser[0]['email'],
                        'type' => 'recipient',
                    );

                    $emailOptions = array(
                        'fromAddress' => 'autoresponse@traffic.org.uk',
                        'fromName' => 'TMS Autoresponse',
                        'recipients' => $recipients,
                        'emailSubject' => 'User Account Password Reset', 
                        'emailPriority' => 1, 
                        'emailBody' => $emailBody,
                        'emailFooter' => getEmailSignature(-1)
                    );        
                    $rstSendEmail = sendEmail($emailOptions);     
                    
					if($rstSendEmail['state'] == 'danger') {
						$oTemplate->setAlert('Email failed to send!.'.$oMail->ErrorInfo, 'danger');
						$oTemplate->load(SITE_VIEWS.'v_login.php');
					} else {
						$oTemplate->setAlert('An email has been sent to '.$oTemplate->getData('inpEmail').' with the instructions on how to reset the password.', 'success');
						$oTemplate->redirect(SITE_URL.'pages/login.php');
					}
				} else {
					$oTemplate->setAlert('Failed to reset password.', 'danger');
					$oTemplate->load(SITE_VIEWS.'v_login.php');
				}
			}
		}
	} else {
		$oTemplate->load(SITE_VIEWS.'v_login.php');
		exit();
	}
?>