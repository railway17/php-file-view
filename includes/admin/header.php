<?php 
	$pageTitle = $this->getMetaData('pageTitle'); if(!empty($pageTitle)) { $metaPageTitle = $pageTitle; } else { $metaPageTitle = SITE_TITLE; }
	$urlProtocol = ($_SERVER["HTTPS"] == 'on') ? 'https' : 'http';
?>
<!DOCTYPE html>
<html lang="en-gb">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
		<meta name="description" content="">
		<meta name="author" content="CSL Group Services Ltd">
        <meta name="robots" content="noindex" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <?php $metaCode = $this->getIncludes('metaCode'); if(!empty($metaCode)) { echo $metaCode; } ?>
        
		<title> <?php echo $metaPageTitle; ?> </title>
		
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_CSS_URL; ?>bootstrap.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_CSS_URL; ?>font-awesome-pro.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_CSS_URL; ?>smartadmin-production-modified-plugins.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_CSS_URL; ?>smartadmin-production.min.css">
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_CSS_URL; ?>smartadmin-skins.min.css">
        <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $urlProtocol.'://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,300,400,700'; ?>">
		<?php $cssIncludes = $this->getIncludes('cssInclude'); if(!empty($cssIncludes)) { echo $cssIncludes; } ?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_CSS_URL; ?>custom.plugins.min.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo ASSETS_CSS_URL; ?>custom-style.min.css" />

		<link rel="shortcut icon" href="<?php echo ASSETS_IMG_URL; ?>favicon/tms-favicon.png">
		<link rel="icon" href="<?php echo ASSETS_IMG_URL; ?>favicon/tms-favicon.png">

		<link rel="apple-touch-icon" href="<?php echo ASSETS_IMG_URL; ?>splash/sptouch-icon-iphone.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php echo ASSETS_IMG_URL; ?>splash/touch-icon-ipad.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php echo ASSETS_IMG_URL; ?>splash/touch-icon-iphone-retina.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php echo ASSETS_IMG_URL; ?>splash/touch-icon-ipad-retina.png">

		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black">

		<link rel="apple-touch-startup-image" href="<?php echo ASSETS_IMG_URL; ?>splash/ipad-landscape.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:landscape)">
		<link rel="apple-touch-startup-image" href="<?php echo ASSETS_IMG_URL; ?>splash/ipad-portrait.png" media="screen and (min-device-width: 481px) and (max-device-width: 1024px) and (orientation:portrait)">
		<link rel="apple-touch-startup-image" href="<?php echo ASSETS_IMG_URL; ?>splash/iphone.png" media="screen and (max-device-width: 320px)">
	</head>
	<body class="menu-on-top fixed-header fixed-navigation fixed-ribbon">
        <header id="header">
            <div id="logo-group">
                <span id="logo"> <img src="<?php echo ASSETS_IMG_URL.'logos/tms-erp-logo.png'; ?>" alt="<?php echo SITE_NAME.' ERP Logo' ?>"> </span>
            </div>
            <div class="pull-right">
				<div id="hide-menu" class="btn-header pull-right">
					<span> <a href="javascript:void(0);" data-action="toggleMenu" title="Collapse Menu"><i class="fal fa-reorder"></i></a> </span>
				</div>
                <ul id="mobile-profile-img" class="header-dropdown-list hidden-xs padding-10">
					<li class="">
						<div id="user-menu" class="btn-header transparent pull-right">
							<span>
								<a href="#" class="dropdown-toggle no-margin userdropdown" data-toggle="dropdown">
									<i class="fal fa-user"></i>
								</a>
								<ul class="dropdown-menu pull-right">
									<li>
										<a href="<?php echo SITE_URL.'pages/logout.php'; ?>" class="padding-10 padding-top-0 padding-bottom-0" data-action="userLogout"><i class="fal fa-fw fa-sign-out"></i> <strong>Logout</strong></a>
									</li>
								</ul>
							</span>
						</div>
					</li>
				</ul>
                <div id="logout" class="btn-header transparent pull-right">
                    <span> <a href="<?php echo SITE_URL.'logout.php'; ?>" title="Sign Out" data-action="userLogout" data-logout-msg="You can improve your security further after logging out by closing this opened browser"><i class="fal fa-fw fa-sign-out"></i></a> </span>
                </div>
                <div id="fullscreen" class="btn-header transparent pull-right">
                    <span> <a href="javascript:void(0);" title="Full Screen" data-action="launchFullscreen"><i class="fal fa-fw fa-arrows"></i></a> </span>
                </div>
            </div>
        </header>