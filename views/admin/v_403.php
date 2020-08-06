<?php

	$content = '
	<section id="widget-grid">
		<div class="row text-center" style="margin-top: 50px;">
			<span class="col-xs-12 col-sm-12 col-md-3 col-lg-3"></span>
			<span class="col-xs-12 col-sm-12 col-md-6 col-lg-6" style="position: relative; display: block;">
				<img src="'.ASSETS_IMG_URL.'403-restricted-access.png" alt="403 Restricted Access" class="text-center" style="margin-bottom:20px;" />
				<h1 class="text-danger"><strong>Access Denied/Forbidden</strong></h1>
				<blockquote>
					<p style="margin-bottom:20px;">The page or resource you were trying to reach is restricted!<br/>If you require access to this content, please contact your system administrator.</p>
					<footer><a href="javascript:void(0);" onclick="history.back(-1)">Click to return to previous page</a></footer>
				</blockquote>
			</span>
			<span class="col-xs-12 col-sm-12 col-md-3 col-lg-3"></span>
		</div>
	</section>';

	include(ADMIN_VIEWS.'v_template.php');

?>