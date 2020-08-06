<?php

	require_once('../library/config.php');
	require_once(ASSETS_PHP_ROOT.'phpmailer/'.PHP_MAILER_VERS.'/class.phpmailer.php');

	$footerCode = '
	<script type="text/javascript">
		$(document).ready(function(){							
			
			$("#resetPassword").on("init.field.fv", function(e, data) {
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
					newPassword: {
						validators: {
							notEmpty: {
								message: "The password field is required"
							},
							identical: {
								field: "newPassword2",
								message: "The password and its confirm are not the same"
							},
							regexp: {
								regexp: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/i,
								message: "Password must have: Lowecase, Uppercase, Symbol And have more than 8 characters.",
							}
						}
					},
					newPassword2: {
						validators: {
							notEmpty: {
								message: "The confirm password field is required"
							},
							identical: {
								field: "newPassword",
								message: "The password and its confirm are not the same"
							},
							regexp: {
								regexp: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})/i,
								message: "Password must have: Lowecase, Uppercase, Symbol And have more than 8 characters.",
							}
						}
					},
					authCode: {
						validators: {
							notEmpty: {
								message: "The Code field is required"
							}
						}
					}
				}
			}).on("err.field.fv", function(e, data) {
				if (data.fv.getInvalidFields().length > 0) {
					$("#btnResetPassword").prop("disabled", true);
				}
			}).on("success.field.fv", function(e, data) {
				if (data.fv.getInvalidFields().length <= 0) {
					$("#btnResetPassword").prop("disabled", false);
				}
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
	
	$userID = isset($_GET['userid']) ? $_GET['userid'] : NULL;
		
	if(isset($_POST['resetPassword'])) {
		$Auth = new Auth();
		$oTemplate->setData('inpAuthCode', $_POST['authCode']);
		$oTemplate->setData('inpPassword1', $_POST['newPassword']);
		$oTemplate->setData('inpPassword2', $_POST['newPassword2']);
		if ($_POST['authCode'] == '' || $_POST['newPassword'] == '' || $_POST['newPassword2'] == '') {
			if ($_POST['email'] == '') { 
				$oTemplate->setData('errorEmail', '* required field!');
				$errorMsg[] = 'Email';
			}
			if ($_POST['newPassword'] == '') { 
				$oTemplate->setData('errorPassword1', '* required field!');
				$errorMsg[] = 'Password 1';
			}
			if ($_POST['newPassword2'] == '') { 
				$oTemplate->setData('errorPassword2', '* required field!');
				$errorMsg[] = 'Password 2';
			}
			if(!empty($errorMsg)) {
				$countErr = count($errorMsg);
				$errorMsg = implode(", ",$errorMsg);
				if($countErr > 0) {
					$oTemplate->setAlert('You must enter your '.$errorMsg.' in order to reset your password','danger');
				}
				$oTemplate->load(SITE_VIEWS.'v_password-reset.php');
			}
		} else {
			//validate the code 
			//make sure passwords match 
			//reset and redirect to login page
			
			$oPasswordReset = new PasswordReset();
			$chkCodeIsValid = $oPasswordReset->getAll("AND authCode = ".$oTemplate->getData('inpAuthCode')."");
			if($chkCodeIsValid) {
				if($chkCodeIsValid[0]['status'] != 'used') {
					//check code has not expired 
					$datetime1 = new DateTime(date('Y-m-d H:i:s'));
					$datetime2 = new DateTime($chkCodeIsValid[0]['createdDate']);
					$interval = $datetime1->diff($datetime2);
					$elapsed = $interval->format('%H:%i:%s');
					
					if(strtotime($elapsed) <= strtotime('00:30:00')) {
						//code exists and code is valid 
						//check passwords match 
						//reset and redirect to login screen.

						if($oTemplate->getData('inpPassword1') == $oTemplate->getData('inpPassword2')){
							$userSalt = getRandomPassSalt(10);
							$authSalt = $oAuth->getCustPortalSalt();

							$passwordHash = password_hash($oTemplate->getData('inpPassword1').$userSalt.$authSalt, PASSWORD_BCRYPT);

							$passwordData = array('password' => $passwordHash, 'salt' => $userSalt );
							$rstUser = $oUser->update($passwordData, $chkCodeIsValid[0]['userID']);   
							if($rstUser) {
								//update the code to used so that it cant be used again.<br>
								$resetData = array('status' => 'used');
								$rstReset = $oPasswordReset->update($resetData, $chkCodeIsValid[0]['resetID']);
								$oTemplate->setAlert('Your Password has been successfully reset.', 'success');
								$oTemplate->redirect(SITE_URL.'pages/login.php');
							} else {
								$oTemplate->setAlert('The new Password is the same as the current password on this account.','info');
								$oTemplate->load(SITE_VIEWS.'v_password-reset.php');
							} 
						} else {
							$oTemplate->setAlert('The Passwords do not Match!.','danger');
							$oTemplate->load(SITE_VIEWS.'v_password-reset.php');
						} 
					} else {
						$oTemplate->setAlert('The Password reset Code has Expired please request a new code.','danger');
						$oTemplate->load(SITE_VIEWS.'v_password-reset.php');
					}
				} else {
					$oTemplate->setAlert('The Password reset Code has already been used please request a new code.','danger');
					$oTemplate->load(SITE_VIEWS.'v_password-reset.php');
				}
			} else {
				$oTemplate->setAlert('The Password reset Code is invalid please use a valid code.','danger');
				$oTemplate->load(SITE_VIEWS.'v_password-reset.php');
			}
		}
	} else {
		$oTemplate->load(SITE_VIEWS.'v_password-reset.php');
		exit();
	}

?>