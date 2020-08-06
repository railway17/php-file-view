<?php
    require_once('../library/config.php');
	require_once(ASSETS_PHP_ROOT.'phpmailer/'.PHP_MAILER_VERS.'/class.phpmailer.php');

    $oAccessODBC = new AccessODBC();
    $objCustomer = $oAccessODBC->getSalesLedger('NETWORK');
    var_dump($objCustomer);