<?php

	$content = '
	<div class="row">
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="min-height:70%;min-height:70vh;display:flex;justify-content:center;align-items: center;">
            <div class="jarviswidget jarviswidget-color-darken" data-widget-deletebutton="false" data-widget-editbutton="false" style="width:23%;">
				<header role="heading">
					<span class="widget-icon"><i class="fal fa-lg fa-fw fa-key"></i></span>
					<h2>Login Verification</h2>
				</header>
				<div role="content">
					<div class="widget-body" style="padding-bottom:0px;">
						<form id="verifyLogin" name="verifyLogin" role="form" method="POST" action="">
							<div class="form-group row text-center">
								<img src="'.ASSETS_LOGO_URL.'tms-logo-md.png" alt="'.SITE_NAME.'" ERP Logo">
								<div class="col-sm-12">
									<h2> 2-Step Verification </h2>
									<p> A verification code has been sent to you via SMS.<br>Please verify that 6 digit code below to continue.
								</div>
							</div>
							<div class="form-group row">
								<div class="col-sm-12">
									<input type="text" class="form-control input-sm" id="verificationCode" name="verificationCode" value="" placeholder="6 digit code"/>
								</div>
							</div>
							<div class="modal-footer row" style="padding:13px;background-color:#f9f6f6">
								<div class="form-group row" style="margin-bottom:0px;">
									<div class="col-sm-6 pull-left">
										<input id="resendCode" name="resendCode" class="btn btn-link pull-left" style="margin: 0 0 0px;font-weight:700;font-size: 13px;line-height: 1.42857143;" type="submit" value="Resend Code" />
									</div>
									<div class="col-sm-6 pull-right">
										<input id="btnLogin" name="login" class="btn btn-primary" type="submit" value="Login" />
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</article>
	</div>';
		
	include(SITE_VIEWS.'v_template.php');

?>