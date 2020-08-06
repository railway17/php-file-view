<?php

    spl_autoload_register(function ($className) {
        $ds = DIRECTORY_SEPARATOR;
        $namespace = str_replace('\\', $ds, __NAMESPACE__);
        $className = str_replace('\\', $ds, $className);
        $coreClassFilePath = ''.DIR_ROOT.'models'.$ds.(empty($namespace) ? '' : $namespace.$ds).$className.'.php';
        if (file_exists($coreClassFilePath)) {
            include_once($coreClassFilePath);
        }
    });
	
 	function multiArrayMap($func, $array)
	{
		$newArray = array();
		if($array) {
			foreach($array as $key => $value) {
				$newArray[$key] = (is_array($value) ? multiArrayMap($func, $value) : $func($value));
			}
		}
		return $newArray;
	}
    function multiArrayMap2($func, $array)
	{
		$newArray = array();
        if($array) {
            foreach($array as $key => $value){
                $newArray[$key] = (is_array($value) ? multiArrayMap2($func, $value) : $func(str_replace('\r\n',PHP_EOL,$value)));
            }
		}
		return $newArray;
	}
    function multiArrayMap3($func, $array)
	{
		$newArray = array();
		foreach($array as $key => $value)
		{
			$newArray[$key] = (is_array($value) ? multiArrayMap3($func, $value) : $func(str_replace('<br>',PHP_EOL,$value)));
		}
		return $newArray;
		
	}
	function multiArrayMapSantize($array)
	{
		$newArray = array();
		if($array) {
			foreach($array as $key => $value) {
				$newArray[$key] = (is_array($value) ? multiArrayMapSantize($value) : htmlspecialchars(stripslashes($value), ENT_QUOTES));
			}
		}
		return $newArray;
	}

	function stringSnippet($text,$limit){
		$explode = explode(' ',$text);
		$string  = '';
		$dots = '...';

		if(count($explode) <= $limit){
			$dots = '';
			$string = $text;
			return $string.$dots;
		}
		for($i=0; $i<$limit; $i++){
			$string .= $explode[$i]." ";
		}
		return $string.$dots;
	}

    function sqlEscape($string) {
        global $oDatabase;
        if ($oDatabase) {
            return $oDatabase->escape($string);
        } else {
            return addslashes($string);
        }
    }

	function santizeHTML($string) {
		$string = str_replace('\r\n',PHP_EOL,$string);
		return htmlspecialchars(stripslashes($string), ENT_QUOTES, "UTF-8");
	}

	function santizeURL($string) {
		return urlencode($string);
	}

	function santizeJAVA($string) {
		return json_encode($string);
	}

	function fnTimesheetSort( $a, $b ) {
		return strtotime($a["date"]) - strtotime($b["date"]);
	}

	function urlDecodeToArray($str) {
		$str = trim($str, '"');
		foreach (explode('&', $str) as $chunk) {
			$param = explode("=", $chunk);
			if ($param) {
				// search string for array elements and look for key-name if exists
				preg_match('#\[(.+?)\]$#', urldecode($param[0]), $with_key);
				preg_match('#\[\]$#', urldecode($param[0]), $no_key);
				$mkey = preg_split('/\[/', urldecode($param[0]));
				// converts to array elements with numeric key
				if ($no_key) {
					$data[$mkey[0]][] = urldecode($param[1]);
				}
				// converts to array elements with named key
				if ($with_key) {
					$data[$mkey[0]][$with_key[1]] = urldecode($param[1]);
				}
				if (!$no_key && !$with_key) {
					$data[urldecode($param[0])] = urldecode($param[1]);
				}

			}
		}
		return $data;
	}

	function base64_url_encode($input) {
		return strtr(base64_encode($input), '+/=', '._-');
	}

	function base64_url_decode($input) {
		return base64_decode(strtr($input, '._-', '+/='));
	}

    function encrypt($decrypted, $password, $salt = 'H7@^T$s=v+3;o:#45-p!')
    {
        $key=substr(hash('SHA256', $salt . $password, true), 0, 16);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($decrypted, 'aes-256-cbc', $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    function decrypt($encrypted, $password, $salt = 'H7@^T$s=v+3;o:#45-p!')
    {
        $key=substr(hash('SHA256', $salt . $password, true), 0, 16);
        list($encryptedData, $iv) = explode('::', base64_decode($encrypted), 2);
        return openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
    }

	function DB_ENCRYPT($column){
        if($column != '' || $column != NULL){
            global $oDatabase;
            $stmt = $oDatabase->rawQuery("SELECT AES_ENCRYPT('".$column."', '".ENCRYPTION_KEY."') AS 'result'");
            if (count($stmt) > 0){
                return $stmt[0]['result'];
            } else {
                return NULL;
            }    
        } else {
            return NULL;
        }
		
	}

    function binarySidToString($binSid)
    {
        $sidHex = unpack('H*hex', $binSid)['hex'];
        $subAuths = unpack('H2/H2/n/N/V*', $binSid);
        $revLevel = hexdec(substr($sidHex, 0, 2));
        $authIdent = hexdec(substr($sidHex, 4, 12));
        return 'S-'.$revLevel.'-'.$authIdent.'-'.implode('-', $subAuths);
    }

    function binaryGuidToHexString($binGUID)
    {
        if (trim($binGUID) == '' || is_null($binGUID)) {
            return;
        }

        $str_hexGUID = '';
        $hexGUID = bin2hex($binGUID);
        for ($k = 1; $k <= 4; ++$k) {
            $str_hexGUID .= substr($hexGUID, 8 - 2 * $k, 2);
        }
        $str_hexGUID .= '-';
        for ($k = 1; $k <= 2; ++$k) {
            $str_hexGUID .= substr($hexGUID, 12 - 2 * $k, 2);
        }
        $str_hexGUID .= '-';
        for ($k = 1; $k <= 2; ++$k) {
            $str_hexGUID .= substr($hexGUID, 16 - 2 * $k, 2);
        }
        $str_hexGUID .= '-' . substr($hexGUID, 16, 4);
        $str_hexGUID .= '-' . substr($hexGUID, 20);

        return strtoupper($str_hexGUID);
    }

    function hexStringToHexGuid($str_hexGUID)
    {
        $hexGUID = '\\'.substr($str_hexGUID, 6, 2).'\\'.substr($str_hexGUID, 4, 2).'\\'.substr($str_hexGUID, 2, 2).'\\'.substr($str_hexGUID, 0, 2);
        $hexGUID = $hexGUID .'\\'. substr($str_hexGUID, 11, 2).'\\'.substr($str_hexGUID, 9, 2);
        $hexGUID = $hexGUID .'\\'. substr($str_hexGUID, 16, 2).'\\'.substr($str_hexGUID, 14, 2);
        $hexGUID = $hexGUID .'\\'. substr($str_hexGUID, 19, 2).'\\'.substr($str_hexGUID, 21, 2);
        $hexGUID = $hexGUID .'\\'. substr($str_hexGUID, 24, 2).'\\'. substr($str_hexGUID, 26, 2).'\\'. substr($str_hexGUID, 28, 2).'\\'. substr($str_hexGUID, 30, 2).'\\'. substr($str_hexGUID, 32, 2).'\\'. substr($str_hexGUID, 34, 2);
        return $hexGUID;
    }
	
		function fromCharCode($num) {
    	return mb_convert_encoding('&#' . intval($num) . ';', 'UTF-8', 'HTML-ENTITIES');
	}

	function getFileSize($file, $type)
	{
	   	switch($type)
		{
		  	case "KB":
			 	$filesize = filesize($file) * .0009765625; // bytes to KB
		  	break;
		  	case "MB":
			 	$filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
		  	break;
		  	case "GB":
			 	$filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
		  	break;
	   	}
	   	if($filesize <= 0)
	   	{
			return $filesize = 'unknown file size';
		}
		else
		{
			return round($filesize, 2).' '.$type;
		}
	}

	function getRandomString($limit = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randStr = '';
		for ($i = 0; $i < $limit; $i++) {
			$randStr .= $characters[rand(0, strlen($characters))];
		}
		return $randStr;
	}
	function getRandomNumbers($limit = 6)
	{
		$characters = '01234567890123456789';
		$randStr = '';
		$charactersLength = strlen($characters);
		for ($i = 0; $i < $limit; $i++) {
			$randStr .= $characters[rand(0, $charactersLength -1)];
		}
		return $randStr;
	}

	function getRandomPassSalt($limit = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!$%^**(--!!??}][#@;{.';
		$randStr = '';
		for ($i = 0; $i < $limit; $i++) {
			$randStr .= $characters[rand(0, strlen($characters))];
		}
		return $randStr;
	}

    function get_allowed_parameters($allowed_params = array())
    {
        $allowed_array = array();
        foreach ($allowed_params as $param) {
            if (isset($_REQUEST[$param])) {
                $allowed_array[$param] = $_REQUEST[$param];
            } else {
                $allowed_array[$param] = null;
            }
        }
    }

    function has_presence($value)
    {
        $trimmed_value = trim($value);
        return isset($trimmed_value) && $trimmed_value !== '';
    }

    function has_length($value, $options = array())
    {
        if (isset($options['max']) && (strlen($value) > (int)$options['max'])) {
            return false;
        }
        if (isset($options['min']) && (strlen($value) < (int)$options['min'])) {
            return false;
        }
        if (isset($options['exact']) && (strlen($value) != (int)$options['exact'])) {
            return false;
        }
        return true;
    }

    function has_format_matching($value, $regex='//')
    {
        return preg_match($regex, $value);
    }

    function has_number($value, $options = array())
    {
        if (!is_numeric($value)) {
            return false;
        }
        if (isset($options['max']) && ($value > (int)$options['max'])) {
            return false;
        }
        if (isset($options['min']) && ($value < (int)$options['min'])) {
            return false;
        }
        return true;
    }

    function request_is_get()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    function request_is_post()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    function csrf_token()
    {
        return md5(uniqid(rand(), true));
    }

    function create_csrf_token()
    {
        $token = csrf_token();
		$_SESSION['csrf_token'] = array();
        $_SESSION['csrf_token'][] = $token;
		$_SESSION['csrf_token_time'] = array();
        $_SESSION['csrf_token_time'][$token] = time();
        return $token;
    }

	function append_csrf_token()
    {
        $token = csrf_token();
        $_SESSION['csrf_token'][] = $token;
        $_SESSION['csrf_token_time'][$token] = time();
        return $token;
    }

    function destroy_csrf_token()
    {
		if (($key = array_search($_POST['csrf_token'], $_SESSION['csrf_token'])) !== false) {
			unset($_SESSION['csrf_token'][$key]);
			unset($_SESSION['csrf_token_time'][$_POST['csrf_token']]);
		}
        return true;
    }

	function delete_csrf_token($token)
    {
		
		if (($key = array_search($token, $_SESSION['csrf_token'])) !== false) {
			unset($_SESSION['csrf_token'][$key]);
		}
		unset($_SESSION['csrf_token_time'][$token]);
        return true;
    }

    function csrf_token_tag()
    {
		if($_SESSION['csrf_token']) {
			$token = append_csrf_token();
		} else {
			$token = create_csrf_token();
		}
       
        return '<input type="hidden" name="csrf_token" value="'.$token.'" />';
    }

    function csrf_token_is_valid()
    {
        if (isset($_POST['csrf_token'])) {
            $user_token = $_POST['csrf_token'];
            $stored_tokens = $_SESSION['csrf_token'];
			if (in_array($user_token, $stored_tokens)) {
				return true;
			} else {
				return false;
			}
        } else {
            return false;
        }
    }

    function csrf_token_is_invalid()
    {
        if (!csrf_token_is_valid()) {
            die('CSRF token validation failed.');
        }
    }

    function csrf_token_is_recent()
    {
        $max_elapsed = 60 * 60 * 24; // 1 day
        if (isset($_SESSION['csrf_token_time'][$_POST['csrf_token']])) {
            $stored_time = $_SESSION['csrf_token_time'][$_POST['csrf_token']];
            return ($stored_time + $max_elapsed) >= time();
        } else {
            destroy_csrf_token();
            return false;
        }
    }

    function request_is_same_domain()
    {
        if (!isset($_SERVER['HTTP_REFERER'])) {
            return false;
        } else {
            $referer_host = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
            $server_host = $_SERVER['HTTP_HOST'];
            return ($referer_host == $server_host) ? true : false;
        }
    }
	
	function getEmailGreeting()
	{
		$hour = date('H', time());
		if( $hour > 1 && $hour <= 11) {
			$emailGreeting = 'Good Morning';
		} else if($hour > 11 && $hour <= 17) {
			$emailGreeting = 'Good Afternoon';
		} else if($hour > 17 && $hour <= 23) {
			$emailGreeting = 'Good Evening';
		} else {
			$emailGreeting = 'Good Day';
		}
		
		return $emailGreeting;
	}

    function sendEmail($emailOptions){
        $oMail = new PHPMailer(true);
        try {
            $oMail->IsSMTP();
            $oMail->Host = MAIL_HOST;
            $oMail->Port = MAIL_PORT;
            $oMail->CharSet = MAIL_CHARSET;
            $oMail->SetFrom($emailOptions['fromAddress'], $emailOptions['fromName']);
            if($emailOptions['replyToEmail'] != ''){
                $oMail->AddReplyTo($emailOptions['replyToEmail'], $emailOptions['replyToName']);	    
            }
            if($emailOptions['recipients'] && DEV_MODE == false){
                foreach($emailOptions['recipients'] as $recipient){
                    if($recipient['email'] != ''){
                        $arrRecipients[] = $recipient['email'];
                        if($recipient['type'] == 'recipient'){
                            $oMail->AddAddress($recipient['email'], $recipient['name']);       
                        } else if($recipient['type'] == 'carbonCopy'){
                            $oMail->AddCC($recipient['email'], $recipient['name']);       
                        } else if($recipient['type'] == 'blankCarbonCopy'){
                            $oMail->AddBCC($recipient['email'], $recipient['name']);       
                        }
                    }
                }
            }
            $oMail->AddBCC('webmaster@traffic.org.uk', 'TMS Webmaster');           
            $oMail->Subject = $emailOptions['emailSubject'];
            $oMail->Priority = $emailOptions['emailPriority'];
            if($emailOptions['emailFooter'] != NULL){
                $oMail->MsgHTML($emailOptions['emailBody'].$emailOptions['emailFooter']['footerHTML']);
                if($emailOptions['emailFooter']['emailLogos']){
                    foreach($emailOptions['emailFooter']['emailLogos'] as $logo){
                        $oMail->AddEmbeddedImage(UPLOADS_ROOT.$logo['link'], $logo['name']);	
                    }
                }    
            } else {
                $oMail->MsgHTML($emailOptions['emailBody']);    
            }
            if($emailOptions['attachments']){
                foreach($emailOptions['attachments'] as $attachment){
                    if(file_exists($attachment['fullPath'])) {
                        $oMail->AddAttachment($attachment['fullPath']);
                        switch($attachment['root']) {
				            case UPLOADS_ROOT:
                                $root = 'UPLOADS_ROOT';
                                break;
                            case REPORTS_ROOT:
                                $root = 'REPORTS_ROOT';
                                break;
                            default: 
                                $root = '';
                                break;
                        }
                        
                        $attachedFiles[] = array (
                            'docFileName' => $attachment['fileName'],
                            'docFilePath' => $attachment['filePath'],
                            'root' => $root
                        );
                    }    
                }
            }
            
            if($emailOptions['icalContent'] != '' && $emailOptions['icalContent'] != NULL){
                $oMail->addStringAttachment($emailOptions['icalContent'],'ical.ics','base64','text/calendar');
            }
            
            sleep(rand(1,5));
            $oMail->Send();
            sleep(rand(1,5));
            $resultArray = array (
                'state' => 'success',
                'message' => 'Email has been sent successfully!'
            );
            $emailLogData = array (
                'dateTime' => date('Y-m-d H:i:s'),
                'sender' => $emailOptions['fromAddress'],
                'recipients' => (($arrRecipients) ? json_encode( $arrRecipients) : NULL),
                'subject' => $emailOptions['emailSubject'],
                'body' => $emailOptions['emailBody'],
                'attachments' => (($attachedFiles) ? json_encode( $attachedFiles) : NULL),
                'status' => 'Success',
                'response' => 'Email has been sent successfully!',
                'sentBy' => $_SESSION['userID'],
            ); 
            $oEmailLog = new EmailLog();
            $logID = $oEmailLog->insert($emailLogData, true);
        } catch(phpmailerException $e) {
            $sendAltEmail = true;
            $resultArray = array (
                'state' => 'danger',
                'message' => $e->errorMessage()
            );
            $emailLogData = array (
                'dateTime' => date('Y-m-d H:i:s'),
                'sender' => $emailOptions['fromAddress'],
                'recipients' => (($arrRecipients) ? json_encode($arrRecipients) : NULL),
                'subject' => $emailOptions['emailSubject'],
                'body' => $emailOptions['emailBody'],
                'attachments' => (($attachedFiles) ? json_encode( $attachedFiles) : NULL),
                'status' => 'Failed',
                'response' => $e->getMessage(),
                'sentBy' => $_SESSION['userID'],
            ); 
            $oEmailLog = new EmailLog();
            $logID = $oEmailLog->insert($emailLogData, true);
        } catch (Exception $e) {
            $sendAltEmail = true;
            $resultArray = array (
                'state' => 'danger',
                'message' => $e->getMessage()
            );
            $emailLogData = array (
                'dateTime' => date('Y-m-d H:i:s'),
                'sender' => $emailOptions['fromAddress'],
                'recipients' => (($arrRecipients) ? json_encode($arrRecipients) : NULL),
                'subject' => $emailOptions['emailSubject'],
                'body' => $emailOptions['emailBody'],
                'attachments' => (($attachedFiles) ? json_encode( $attachedFiles) : NULL),
                'status' => 'Failed',
                'response' => $e->getMessage(),
                'sentBy' => $_SESSION['userID'],
            ); 
            $oEmailLog = new EmailLog();
            $logID = $oEmailLog->insert($emailLogData, true);
        }
        
        if($sendAltEmail == true){
            $oMail = new PHPMailer(true);
            try {
               $oMail->IsSMTP();
                $oMail->Host = ALT_MAIL_HOST;
                $oMail->Port = MAIL_PORT;
                $oMail->CharSet = MAIL_CHARSET;
                $oMail->SetFrom($emailOptions['fromAddress'], $emailOptions['fromName']);
                if($emailOptions['recipients'] && DEV_MODE == false){
                    foreach($emailOptions['recipients'] as $recipient){

                        if($recipient['email'] != ''){
                            $arrRecipients[] = $recipient['email'];
                            if($recipient['type'] == 'recipient'){
                                $oMail->AddAddress($recipient['email'], $recipient['name']);       
                            } else if($recipient['type'] == 'carbonCopy'){
                                $oMail->AddCC($recipient['email'], $recipient['name']);       
                            } else if($recipient['type'] == 'blankCarbonCopy'){
                                $oMail->AddBCC($recipient['email'], $recipient['name']);       
                            }
                        }
                    }
                }
                $oMail->AddBCC('webmaster@traffic.org.uk', 'TMS Webmaster');           
                $oMail->Subject = $emailOptions['emailSubject'];
                $oMail->Priority = $emailOptions['emailPriority'];
                if($emailOptions['emailFooter'] != NULL){
                    $oMail->MsgHTML($emailOptions['emailBody'].$emailOptions['emailFooter']['footerHTML']);
                    if($emailOptions['emailFooter']['emailLogos']){
                        foreach($emailOptions['emailFooter']['emailLogos']as $logo){
                            $oMail->AddEmbeddedImage(ASSETS_IMG_ROOT.$logo['link'], $logo['name']);	
                        }
                    }    
                } else {
                    $oMail->MsgHTML($emailOptions['emailBody']);    
                }
                if($emailOptions['attachments']){
                    foreach($emailOptions['attachments'] as $attachment){
                        if(file_exists($attachment)) {
                            $oMail->AddAttachment($attachment);
                             switch($attachment['root']) {
                                case UPLOADS_ROOT:
                                    $root = 'UPLOADS_ROOT';
                                    break;
                                case REPORTS_ROOT:
                                    $root = 'REPORTS_ROOT';
                                    break;
                                default: 
                                    $root = '';
                                    break;
                            }
                            $attachedFiles[] = array (
                                'docFileName' => $attachment['fileName'],
                                'docFilePath' => $attachment['filePath'],
                                'root' => $root
                            );
                        }    
                    }
                }
                sleep(rand(1,5));
                $oMail->Send();
                sleep(rand(1,5));
                $resultArray = array (
                    'state' => 'success',
                    'message' => 'Email has been sent successfully!'
                );
                $emailLogData = array (
                    'dateTime' => date('Y-m-d H:i:s'),
                    'sender' => $emailOptions['fromAddress'],
                    'recipients' => (($arrRecipients) ? json_encode( $arrRecipients) : NULL),
                    'subject' => $emailOptions['emailSubject'],
                    'body' => $emailOptions['emailBody'],
                    'attachments' => (($attachedFiles) ? json_encode( $attachedFiles) : NULL),
                    'status' => 'Success',
                    'response' => 'Email has been sent successfully!',
                    'sentBy' => $_SESSION['userID'],
                ); 
                $oEmailLog = new EmailLog();
                $logID = $oEmailLog->insert($emailLogData, true);
            } catch(phpmailerException $e) {
                    $resultArray = array (
                        'state' => 'danger',
                        'message' => $e->errorMessage()
                    );
                    $emailLogData = array (
                    'dateTime' => date('Y-m-d H:i:s'),
                    'sender' => $emailOptions['fromAddress'],
                    'recipients' => (($arrRecipients) ? json_encode( $arrRecipients) : NULL),
                    'subject' => $emailOptions['emailSubject'],
                    'body' => $emailOptions['emailBody'],
                    'attachments' => (($attachedFiles) ? json_encode( $attachedFiles) : NULL),
                    'status' => 'Failed',
                    'response' => $e->getMessage(),
                    'sentBy' => $_SESSION['userID'],
                ); 
                $oEmailLog = new EmailLog();
                $logID = $oEmailLog->insert($emailLogData, true);
            } catch (Exception $e) {
               $resultArray = array (
                    'state' => 'danger',
                    'message' => $e->getMessage()
                );
                $emailLogData = array (
                    'dateTime' => date('Y-m-d H:i:s'),
                    'sender' => $emailOptions['fromAddress'],
                    'recipients' => (($arrRecipients) ? json_encode( $arrRecipients) : NULL),
                    'subject' => $emailOptions['emailSubject'],
                    'body' => $emailOptions['emailBody'],
                    'attachments' => (($attachedFiles) ? json_encode( $attachedFiles) : NULL),
                    'status' => 'Failed',
                    'response' => $e->getMessage(),
                    'sentBy' => $_SESSION['userID'],
                ); 
                $oEmailLog = new EmailLog();
                $logID = $oEmailLog->insert($emailLogData, true);
            }     
        }
        return $resultArray;
    }

	function getEmailSignature($userID = NULL, $departmentID = NULL) 
	{
		
		$emailFooter = '<p style="font-family:Calibri, Arial, sans-serif;font-size:11pt">';
		
		if ($userID != NULL && $departmentID != NULL){
			
            $oUser = new User();
			$dbUser = $oUser->getOne($userID);
            
            $companyID = $dbUser[0]['companyID'];
            
            $oCompany = new Company();
            $dbCompany = $oCompany->getOne($companyID);
            
			$oDepartment = new Department();
			$dbDepartment = $oDepartment->getOne($departmentID);
			if ($dbUser){
				$emailFooter .= 'Kind regards<br><br>'.$dbUser[0]['fullName'].'<br><strong>'.$dbUser[0]['position'].'</strong>';
				$extNumber = $dbDepartment[0]['extNum'];
				$emailAddress = $dbDepartment[0]['email'];	
				if($dbUser[0]['depotID'] > 0) {
					$oDepot = new Depot();
					$dbDepot = $oDepot->getOne($dbUser[0]['depotID']);
					$depotAddress = $dbDepot[0]['depotAddress'];
					if($dbUser[0]['depotID'] != $dbCompany[0]['mainDepot']) {
						$dbDepot = $oDepot->getOne($dbCompany[0]['mainDepot']);
						$headOffice = $dbDepot[0]['depotAddress'];
					}
				} else {
					$oDepot = new Depot();
					$dbDepot = $oDepot->getOne($dbCompany[0]['mainDepot']);
					$depotAddress = $dbDepot[0]['depotAddress'];	
				}	
			}
		} elseif ($userID != NULL && $departmentID == NULL){
			if($userID == -1) {
                
                $companyID = 1;
                
                $oCompany = new Company();
                $dbCompany = $oCompany->getOne($companyID);
                
				$oDepot = new Depot();
				$dbDepot = $oDepot->getOne($dbCompany[0]['mainDepot']);
				$depotAddress = $dbDepot[0]['depotAddress'];
				$emailAddress = $dbCompany[0]['infoEmailAddress'];
				$emailFooter .= 'Kind regards<br><br>TMS Autoresponse<br><strong>ERP System</strong>';
			} elseif($userID > 0) {

				$oUser = new User();
				$dbUser = $oUser->getOne($userID);
                
                $companyID = $dbUser[0]['companyID'];
                
                $oCompany = new Company();
                $dbCompany = $oCompany->getOne($companyID);
                
				if ($dbUser){
					$emailFooter .= 'Kind regards<br><br>'.$dbUser[0]['fullName'].'<br><strong>'.$dbUser[0]['position'].'</strong>';
					$extNumber = $dbUser[0]['extNum'];
					$emailAddress = $dbUser[0]['email'];	
					$mobileNumber = $dbUser[0]['workMobile'];	
					if($dbUser[0]['depotID'] > 0) {
						$oDepot = new Depot();
						$dbDepot = $oDepot->getOne($dbUser[0]['depotID']);
						$depotAddress = $dbDepot[0]['depotAddress'];
						if($dbUser[0]['depotID'] != $dbCompany[0]['mainDepot']) {
							$dbDepot = $oDepot->getOne($dbCompany[0]['mainDepot']);
							$headOffice = $dbDepot[0]['depotAddress'];
						}
					} else {
						$oDepot = new Depot();
						$dbDepot = $oDepot->getOne($dbCompany[0]['mainDepot']);
						$depotAddress = $dbDepot[0]['depotAddress'];	
					}	
				} 
			} 
		} elseif($departmentID != NULL && $userID == NULL) {
			$oDepartment = new Department();
			$dbDepartment = $oDepartment->getOne($departmentID);
            
            $companyID = $dbDepartment[0]['companyID'];
                
            $oCompany = new Company();
            $dbCompany = $oCompany->getOne($companyID);
            
			if($dbDepartment[0]['depotID'] > 0) {
				$oDepot = new Depot();
				$dbDepot = $oDepot->getOne($dbDepartment[0]['depotID']);
				$depotAddress = $dbDepot[0]['depotAddress'];
				if($dbDepartment[0]['depotID'] != $dbCompany[0]['mainDepot']) {
					$dbDepot = $oDepot->getOne($dbCompany[0]['mainDepot']);
					$headOffice = $dbDepot[0]['depotAddress'];
				}
			} else {
				$oDepot = new Depot();
				$dbDepot = $oDepot->getOne($dbCompany[0]['mainDepot']);
				$depotAddress = $dbDepot[0]['depotAddress'];	
			}
			$emailFooter .= 'Kind regards<br><br><strong>'.$dbDepartment[0]['name'].'</strong>';
			$extNumber = $dbDepartment[0]['extNum'];
			$emailAddress = $dbDepartment[0]['email'];
		}
        
        $oLogo = new Logo();
        $companyEmailLogo = $oLogo->getCompanyEmailFooterLogo($companyID);
        $logoList = $oLogo->getEmailFooterLogos($companyID);
        
		$emailFooter .= '<br><br><img src="cid:'.$companyEmailLogo['name'].'" /><br>'.$depotAddress.'<br><strong>Tel:</strong> '.$dbCompany[0]['phoneNumber'].' '.(($extNumber > 0) ? ' <strong>Ext:</strong> '.$extNumber : '').' | '.(($mobileNumber != NULL) ? ' <strong>Mob:</strong> '.$mobileNumber.' | ' : '').' <strong>Email:</strong> <a href="mailto:'.(($emailAddress != '') ? $emailAddress : 'info@traffic.org.uk').'">'.(($emailAddress != '') ? $emailAddress : 'info@traffic.org.uk').'</a> | <strong>Website:</strong> <a href="'.$dbCompany[0]['webAddress'].'">'.$dbCompany[0]['webAddress'].'</a>'.(($headOffice != '') ? '<br><strong>Head Office: </strong>'.$headOffice : '').'<br><br>';if($logoList){foreach($logoList as $dbLogo){ $emailFooter .= '<img src="cid:'.$dbLogo['name'].'" />';   } }$emailFooter .= '<br><br><span style="font-weight:bold;font-size:9pt;">Consider the environment – please think before you print this email!</span><br><span style="font-size:9pt;">This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed. If you have received this email in error please notify the system manager. This message contains confidential information and is intended only for the individual named. If you are not the named addressee you should not disseminate, distribute or copy this e-mail. Please notify the sender immediately by e-mail if you have received this e-mail by mistake and delete this e-mail from your system. If you are not the intended recipient you are notified that disclosing, copying, distributing or taking any action in reliance on the contents of this information is strictly prohibited.</span></p>';
		
        $logoList[] = $companyEmailLogo;
        
		$resultArray = array (
			'footerHTML' => $emailFooter,
			'emailLogos' => $logoList
		);
		
		return $resultArray;
	}

    function createObjectFromArray($array){
        $objectData = new stdClass();
        if($array) {
            foreach($array as $key => $value) {
                $objectData->$key = $value;    
            }   
        }
        return $objectData;
    }

    function getDirContents($dir, &$results = array()) {
        $files = scandir($dir);
    
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            
            if (!is_dir($path)) {
                $results[] = str_replace(DIR_ROOT.DOCUMENTS_ROOT.'/', "", str_replace("\\", "/", $path));
            } else if ($value != "." && $value != "..") {
                getDirContents($path, $results);
                $results[] = str_replace(DIR_ROOT.DOCUMENTS_ROOT.'/', "", str_replace("\\", "/", $path))."/";
            }
        }
    
        return $results;
    }

    function checkDocumentRootExist() {
        if(is_dir(DIR_ROOT.DOCUMENTS_ROOT)) {
            return true;
        } else {
            return false;
        };
    }

    function createNewFolder($parentPath, $folderName) {
        $result = mkdir(makeFullFilePath($parentPath.$folderName));
        return $result;
    }

    function renameFolder($path, $newFolderName) {
        if($path[strlen($path) - 1] == '/')
            $path = substr($path, 0, strlen($path) - 1);
        $lastIndex = strrpos($path, '/');
        $pre = substr($path, 0, $lastIndex);
        $newPath = $pre.'/'.$newFolderName;
        $result = rename(makeFullFilePath($path), makeFullFilePath($newPath));
        if($result) {
            return $newPath.'/';
        } else {
            return false;
        }
    }

    function makeFullFilePath($path) {
        if($path[0] == '/') {
            return DIR_ROOT.DOCUMENTS_ROOT.$path;
        } else {
            return DIR_ROOT.DOCUMENTS_ROOT.'/'.$path;
        }
    }

    function FileSizeConvert($bytes)
    {
        $bytes = floatval($bytes);
            $arBytes = array(
                0 => array(
                    "UNIT" => "TB",
                    "VALUE" => pow(1024, 4)
                ),
                1 => array(
                    "UNIT" => "GB",
                    "VALUE" => pow(1024, 3)
                ),
                2 => array(
                    "UNIT" => "MB",
                    "VALUE" => pow(1024, 2)
                ),
                3 => array(
                    "UNIT" => "KB",
                    "VALUE" => 1024
                ),
                4 => array(
                    "UNIT" => "B",
                    "VALUE" => 1
                ),
            );

        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
                break;
            }
        }
        return $result;
    }
