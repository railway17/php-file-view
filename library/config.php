<?php
	
	if (!isset($_SESSION)) {
		session_start();
	}

	require_once('func.global.php');
	require_once('Template.php');

	ini_set('display_errors', 			'On');
	ini_set('error_reporting', 			E_ALL ^ E_NOTICE ^ E_DEPRECATED);
	
	$_SERVER["HTTP_HOST"] = $_SERVER["HTTP_HOST"];
	$urlProtocol = @($_SERVER["HTTPS"] == "on") ? 'https' : 'http';
	
	define('DEV_MODE', 					true);
	define('SITE_NAME', 				'ERP System {DEVELOPMENT}');
	define('SITE_TITLE', 				'ERP System (DEVELOPMENT)');
	
	define('SITE_URL', 					$urlProtocol.'://'.$_SERVER["HTTP_HOST"].'/file/');
	define('PUBLIC_URL', 				$urlProtocol.'://'.$_SERVER["HTTP_HOST"].'/pages/');
	define('ADMIN_URL', 				$urlProtocol.'://'.$_SERVER["HTTP_HOST"].'/pages/admin/');

	define('DIR_ROOT', 					$_SERVER['DOCUMENT_ROOT'].'/file/');
	define('UPLOADS_ROOT', 				'uploads');
	define('REPORTS_ROOT', 				'/reports');
	define('DOCUMENTS_ROOT', 			'uploads/Documents');
	
	define('DB_HOST', 					'localhost');
	define('DB_USER', 					'root');
	define('DB_PASS', 					'');
	define('DB_NAME', 					'dev_tms_erp_db');

	define('DB_TBL_PREFIX', 			'sys_');
	define('COMPANYID',				    '1');

	if(!defined('ENCRYPTION_KEY')) {
		define('ENCRYPTION_KEY', '<INPUT>');
	}
	if(!defined('USER_LIMIT')) {
		define('USER_LIMIT', '9999');
	}

	define('SITE_VERSION',					'Software Version: 5.0.1');
	define('SITE_COPYRIGHT', 				'Copyright &copy; '.date('Y').' CSL Group Services Ltd. All Rights Reserved.');
	define('CURRENT_PAGE_URL', 				$urlProtocol.'://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);

	define('SITE_PAGES', 					DIR_ROOT.'pages/');
	define('SITE_VIEWS', 					DIR_ROOT.'views/');
	define('PUBLIC_PAGES', 					DIR_ROOT.'pages/public/');
	define('PUBLIC_VIEWS', 					DIR_ROOT.'views/public/');
	define('ADMIN_PAGES', 					DIR_ROOT.'pages/admin/');
	define('ADMIN_VIEWS', 					DIR_ROOT.'views/admin/');

	define('INCLUDES_ROOT', 				DIR_ROOT.'includes/');
	define('MODELS_ROOT', 					DIR_ROOT.'models/');
	define('CRONJOB_ROOT', 					DIR_ROOT.'cronjobs/');
	define('AJAX_ROOT', 					DIR_ROOT.'public/ajax/');
	
	define('ASSETS_ROOT', 					DIR_ROOT.'public/');
	define('ASSETS_CSS_ROOT', 				DIR_ROOT.'public/css/');
	define('ASSETS_FONT_ROOT', 				DIR_ROOT.'public/fonts/');
	define('ASSETS_IMG_ROOT', 				DIR_ROOT.'public/img/');
	define('ASSETS_JS_ROOT', 				DIR_ROOT.'public/js/');
	define('ASSETS_PHP_ROOT', 				DIR_ROOT.'public/php/');
	
	define('TEMP_ROOT', 					DIR_ROOT.'../Temp/');
	define('SESSION_ROOT', 					DIR_ROOT.'../Sessions/');

	define('AJAX_URL', 						SITE_URL.'public/ajax/');
	define('REPORTS_URL', 					SITE_URL.'public/reports/');
	define('UPLOADS_URL', 					SITE_URL.'public/uploads/');
	define('CRONJOBS_URL', 					SITE_URL.'cronjobs/');
	
	define('ASSETS_URL', 					SITE_URL.'public/');
	define('ASSETS_CSS_URL', 				SITE_URL.'public/css/');
	define('ASSETS_FONT_URL', 				SITE_URL.'public/fonts/');
	define('ASSETS_IMG_URL', 				SITE_URL.'public/img/');
	define('ASSETS_ICON_URL', 				SITE_URL.'public/img/icons/');
	define('ASSETS_LOGO_URL',				SITE_URL.'public/img/logos/');
	define('ASSETS_JS_URL', 				SITE_URL.'public/js/');
	define('ASSETS_PHP_URL', 				SITE_URL.'public/php/');


	//ACCESS MODE VARIABLES FOR FOLDERS
	define('ACCESS_MODE_NO_ACCESS', 		0);
	define('ACCESS_MODE_READ_PERMISSION', 	1);
	define('ACCESS_MODE_RW_PERMISSION', 	2);
	define('ACCESS_MODE_All_PERMISSION', 	3);
	define('REVISION_ACTION', array(
		'CREATED'=>'Created',
		'UPDATED'=>'Updated',
		'RENAMED'=>'Renamed',
		'REPERMITTED'=>'Permission Changed'
	));

	define('SESSION_TIMEOUT', 				3600);
	
	//3RD PARTY PLUGINS & VERSIONS
	define('BOOTSTRAP_VERS',				'3.2.0');
	define('FONT_AWESOME_VERS',				'4.7.0');
	define('DATATABLES_VERS',				'1.10.18');
	define('JSCOLOR_VERS',					'1.4.3');
	define('JQUERY_LIB_VERS',				'2.2.4');
	define('JQUERY_UI_VERS',				'1.12.1');
	define('JQUERY_VALIDATION_VERS',		'1.13.0');
	define('JQUERY_FILE_UPLOAD_VERS',		'9.8.0');
	define('FULL_CALENDAR_VERS',			'3.9.0');
	define('UPLOADIFY_VERS',				'3.2.1');
	define('UPLOADIFIVE_VERS',				'1.2.2');
	define('DATE_RANGE_PICKER_VERS',		'1.8.7');
	define('MASKED_INPUT_VERS',				'1.3.1');
	define('MASK_MONEY_VERS',				'3.0.2');
	define('PHP_MAILER_VERS',				'5.2.8');
	define('FPDF_VERS',						'1.81');
	define('FPDI_VERS',						'1.5.2');
	define('FANCYBOX_VERS',					'3.5.2');
	define('GOMAP_VERS',					'1.3.3');
	define('PHPEXCEL_VERS',					'1.8.0');
	define('PHPWORD_VERS',					'0.6.2');
	define('PDFMAKE_VERS',					'0.1.18');
	define('STEPWIZARD_VERS',				'2.3');

	$oDatabase = new Database(DB_HOST, DB_USER, DB_PASS, DB_NAME, '3306', 'utf8');
	
	$oTemplate = new Template();
	$oTemplate->setAlertTypes(array('success','warning','danger','info'));
	$oTemplate->setIncludeTypes(array('cssInclude','jsInclude','metaCode','headerCode','footerCode'));
	$oTemplate->setMetaTypes(array('pageTitle','pageDescription','pageTags'));
	
	$oAuth = new Auth();
	$oUser = new User();
	$oActionLog = new ActionLog();

?>