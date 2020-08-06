<?php

	$meta_code = '<meta http-equiv="Refresh" content="3; url='.SITE_URL.'pages/login" />';

	$content = '
		<h1>Log Out Successful!</h1>
		<p>You have been successfully logged out. You will now be redirected to the login page.</p>
		<p>If your browser does not automatically redirect you in a few seconds, <a href="'.SITE_URL.'pages/login">click here</a> to go to the Login page.</p>';

	include(SITE_VIEWS.'v_template.php');

?>