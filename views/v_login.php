<?php
	if($this->getData('isMobile') == true){
		$content.= '
		<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="jarviswidget jarviswidget-color-darken" data-widget-deletebutton="false" data-widget-editbutton="false">
				<header role="heading">
					<h2>Login Authorisation</h2>
				</header>
				<div role="content">
					<div class="widget-body" style="padding-bottom:0px;">
						<form id="checkLogin" name="checkLogin" role="form" method="POST" action="">
							<div id="loginDetails" '.((isset($_POST['resetPassword'])) ? 'style="display:none;"' : '').'>
								<div class="form-group row has-feedback">
									<label for="username" class="col-sm-3 control-label"><strong>Username:</strong></label>
									<div class="col-sm-9 passwordFeedback">
										<div class="input-group">
											<input type="text" class="form-control input-sm" id="username" name="username" value="" />
											<i class="form-control-feedback fal fa-lg fa-asterisk" data-fv-icon-for="username"></i>
											<span class="input-group-addon"><i class="fal fa-lg fa-user"></i></span>
										</div>
									</div>
								</div>
								<div class="form-group row has-feedback">
									<label for="password" class="col-sm-3 control-label"><strong>Password:</strong></label>
									<div class="col-sm-9 passwordFeedback">
										 <div class="input-group">
											<input type="password" class="form-control input-sm" id="password" name="password" value="" autocomplete="new-password" />
											<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-asterisk" data-fv-icon-for="password"></i>
											<span class="input-group-addon"><i class="fal fa-lg fa-key"></i></span>
										</div>
									</div>
								</div>';
								if($this->getData('updateSecurityPhone') == true){
									$content .='
									<div class="form-group row has-feedback">
										<label for="securityPhone" class="col-sm-3 control-label"><strong>Phone Number:</strong></label>
										<div class="col-sm-9 passwordFeedback">
											<div class="input-group">
												<input type="text" class="form-control input-sm" id="securityPhone" name="securityPhone" value="" />
												<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-asterisk" data-fv-icon-for="securityPhone"></i>
												<span class="input-group-addon"><i class="fal fa-lg fa-mobile"></i></span>
											</div>
										</div>
									</div>';
								}
								$content .='
								<div class="modal-footer row" style="padding:13px;background-color:#f9f6f6">
									<div class="form-group row" style="margin-bottom:0px;">
										<div class="col-sm-6 pull-left" style="padding-top: 6px;">
											<p style="margin: 0 0 0px;" class="pull-left"><a href="javascript:void(0);" id="forgottenPassword"><strong>Forgotten Password?</strong></a></p>
										</div>
										<div class="col-sm-6 pull-right">
											<input id="btnLogin" name="login" class="btn btn-primary" type="submit" value="Login" />
										</div>
									</div>
								</div>
							</div>
							<div id="forgottenDetails" '.((isset($_POST['resetPassword'])) ? '' : 'style="display:none;"').'>
								<div class="form-group row has-feedback">
									<label for="email" class="col-sm-3 control-label"><strong>Email:</strong></label>
									<div class="col-sm-9 passwordFeedback">
										<div class="input-group">
											<input type="text" class="form-control input-sm" id="email" name="email" value="" />
											<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-asterisk" data-fv-icon-for="email"></i>
											<span class="input-group-addon"><i class="fal fa-lg fa-envelope"></i></span>
										</div>
									</div>
								</div>
								<div class="modal-footer row" style="padding:13px;background-color:#f9f6f6">
									<div class="form-group row" style="margin-bottom:0px;">
										<div class="col-sm-6" style="padding-top: 6px;">
											<p style="margin: 0 0 0px;" class="pull-left"><a href="javascript:void(0);" id="returnToLogin"><strong>Return to Login?</strong></a></p>
										</div>
										<div class="col-sm-6">
											<input id="btnResetPassword" name="resetPassword" class="btn btn-md btn-danger pull-right" type="submit" value="Reset Password" />
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>	
			</article>
		</div>';	
	} else {
		$content = '
		<div class="row">
			<article class="col-xs-12 col-sm-12 col-md-12 col-lg-12" style="min-height:70%;min-height:70vh;display:flex;justify-content:center;align-items: center;">
				<div class="jarviswidget jarviswidget-color-darken" data-widget-deletebutton="false" data-widget-editbutton="false" style="width:23%;">
					<header role="heading">
						<h2>Login Authorisation</h2>
					</header>
					<div role="content">
						<div class="widget-body" style="padding-bottom:0px;">
							<form id="checkLogin" name="checkLogin" role="form" method="POST" action="">
								<div id="loginDetails" '.((isset($_POST['resetPassword'])) ? 'style="display:none;"' : '').'>
									<div class="form-group row has-feedback">
										<label for="username" class="col-sm-3 control-label"><strong>Username:</strong></label>
										<div class="col-sm-9 passwordFeedback">
											<div class="input-group">
												<input type="text" class="form-control input-sm" id="username" name="username" value="" />
												<span class="input-group-addon"><i class="fal fa-fw fa-user"></i></span>
											</div>
										</div>
									</div>
									<div class="form-group row has-feedback">
										<label for="password" class="col-sm-3 control-label"><strong>Password:</strong></label>
										<div class="col-sm-9 passwordFeedback">
											 <div class="input-group">
												<input type="password" class="form-control input-sm" id="password" name="password" value="" autocomplete="new-password" />
												<span class="input-group-addon"><i class="fal fa-fw fa-lock"></i></span>
											</div>
										</div>
									</div>';
									if($this->getData('updateSecurityPhone') == true){
										$content .='
										<div class="form-group row has-feedback">
											<label for="securityPhone" class="col-sm-3 control-label"><strong>Security Phone:</strong></label>
											<div class="col-sm-9 passwordFeedback">
												<div class="input-group">
													<input type="text" class="form-control input-sm" id="securityPhone" name="securityPhone" value="" />
													<i class="form-control-feedback glyphicon-asterisk glyphicon glyphicon-asterisk" data-fv-icon-for="securityPhone" style=""></i>
													<span class="input-group-addon"><i class="fal fa-fw fa-mobile-phone"></i></span>
												</div>
											</div>
										</div>';
									}
									$content .='
									<div class="modal-footer row" style="padding:13px;background-color:#f9f6f6">
										<div class="form-group row" style="margin-bottom:0px;">
											<div class="col-sm-6 pull-left" style="padding-top: 6px;">
												<p style="margin: 0 0 0px;" class="pull-left"><a href="javascript:void(0);" id="forgottenPassword"><strong>Forgotten Password?</strong></a></p>
											</div>
											<div class="col-sm-6 pull-right">
												<input id="btnLogin" name="login" class="btn btn-primary" type="submit" value="Login" />
											</div>
										</div>
									</div>
								</div>
								<div id="forgottenDetails" '.((isset($_POST['resetPassword'])) ? '' : 'style="display:none;"').'>
									<div class="form-group row has-feedback">
										<label for="email" class="col-sm-3 control-label"><strong>Email:</strong></label>
										<div class="col-sm-9 passwordFeedback">
											<div class="input-group">
												<input type="text" class="form-control input-sm" id="email" name="email" value="" />
												<span class="input-group-addon"><i class="fal fa-fw fa-envelope"></i></span>
											</div>
										</div>
									</div>
									<div class="modal-footer row" style="padding:13px;background-color:#f9f6f6">
										<div class="form-group row" style="margin-bottom:0px;">
											<div class="col-sm-6" style="padding-top: 6px;">
												<p style="margin: 0 0 0px;" class="pull-left"><a href="javascript:void(0);" id="returnToLogin"><strong>Return to Login?</strong></a></p>
											</div>
											<div class="col-sm-6">
												<input id="btnResetPassword" name="resetPassword" class="btn btn-md btn-danger pull-right" type="submit" value="Reset Password" />
											</div>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</article>
		</div>';
	}
		
	include(SITE_VIEWS.'v_template.php');

?>