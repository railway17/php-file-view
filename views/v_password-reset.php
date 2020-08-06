<?php

	$content = '
	<div class="row">
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="min-height:70%;min-height:70vh;display:flex;justify-content:center;align-items: center;">
            <div class="jarviswidget jarviswidget-color-darken" data-widget-deletebutton="false" data-widget-editbutton="false" style="width:25%;">
				<header role="heading">
					<span class="widget-icon"><i class="fal fa-lg fa-fw fa-lock"></i></span>
					<h2>Password Reset</h2>
				</header>
				<div role="content">
					<div class="widget-body" style="padding-bottom:0px;">
						<form id="resetPassword" name="resetPassword" role="form" method="POST" action="">
							<div class="form-group row has-feedback">
								<label for="authCode" class="col-sm-4 control-label"><strong>Reset Code:</strong></label>
								<div class="col-sm-8 inpGrpFeedback">
									<div class="input-group">
										<input type="text" class="form-control input-sm" id="authCode" name="authCode" value="'.$_GET['authcode'].'" />
										<span class="input-group-addon"><i class="fal fa-fw fa-key"></i></span>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label for="newPassword" class="col-sm-4 control-label"><strong>New Password:</strong></label>
								<div class="col-sm-8 inpGrpFeedback">
									<div class="input-group">
										<input type="password" class="form-control input-sm" id="newPassword" name="newPassword" autocomplete="new-password" value="">
										<span class="input-group-addon"><i class="fal fa-fw fa-lock"></i></span>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label for="newPassword2" class="col-sm-4 control-label"><strong>Confirm Password:</strong></label>
								<div class="col-sm-8 inpGrpFeedback">
									<div class="input-group">
										<input type="password" class="form-control input-sm" id="newPassword2" name="newPassword2" autocomplete="new-password" value="">
										<span class="input-group-addon"><i class="fal fa-fw fa-lock"></i></span>
									</div>
								</div>
							</div>
							<div class="modal-footer row" style="padding:13px;background-color:#f9f6f6">
								<div class="form-group row" style="margin-bottom:0px;">
									<div class="col-sm-6" style="padding-top: 6px;">
										<p style="margin: 0 0 0px;" class="pull-left"><a href="'.PUBLIC_URL.'login.php" id="returnToLogin"><strong>Return to Login?</strong></a></p>
									</div>
									<div class="col-sm-6">
										<input id="btnResetPassword" name="resetPassword" class="btn btn-md btn-danger pull-right" type="submit" value="Reset Password" />
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