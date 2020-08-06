<?php
	
	$content = '
	<form class="lockscreen animated flipInY" action="">
		<div class="logo">
			<h1 class="semi-bold"><img src="'.ASSETS_IMG_URL.'logos/'.COMPANY_TAG.'-crm-logo.png" alt="'.COMPANY_NAME.' Logo" /></h1>
		</div>
		<div>
			<img src="'.ASSETS_IMG_URL.'avatars/sunny-big.png" alt="" width="120" height="120" />
			<div>
				<h1>
					<i class="fal fa-user fa-3x text-muted air air-top-right hidden-mobile"></i>
					'.$_SESSION['displayName'].'
					<small>
						<i class="fal fa-lock text-muted"></i> &nbsp;Locked
					</small>
				</h1>
				<p class="text-muted">'.$_SESSION['username'].'</p>
				<div class="input-group">
					<input class="form-control input-sm" type="password" placeholder="Password">
					<div class="input-group-btn">
						<button class="btn btn-primary" type="submit">
							<i class="fal fa-key"></i>
						</button>
					</div>
				</div>
				<p class="no-margin margin-top-5">
					Logged as someone else? <a href="'.SITE_URL.'login.php"> Click here</a>
				</p>
			</div>
		</div>
	</form>';
		
	include(PUBLIC_VIEWS.'template.php');

?>