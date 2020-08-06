<div class="page-footer">
	<div class="row">
		<div class="col-md-6 footer-left" >
			<p class="text-muted credit"><?php echo SITE_COPYRIGHT; ?></p>
		</div>
		<div class="col-md-6 footer-right" >
			<p class="text-muted pull-right" style="cursor:pointer" id="btnRequestChange">&nbsp;&nbsp;| <a href="mailto:support@cslgroupservices.co.uk?Subject=TMS ERP Ticket" target="_top" style="text-decoration:none;"><i class="fal fa-lg fa-fw fa-envelope"></i> Support</a></p>
			<p class="text-muted pull-right" onclick="loadChangeLog()" id="changelog" style="cursor:pointer"><?php echo SITE_VERSION; ?></p>
		</div>
	</div>
</div>
<script src="<?php echo ASSETS_JS_URL; ?>libs/jquery-3.4.1.min.js"></script>
<script src="<?php echo ASSETS_JS_URL; ?>libs/jquery-ui-1.12.1.min.js"></script>
<!-- IMPORTANT: APP CONFIG -->
<script src="<?php echo ASSETS_JS_URL; ?>app.config.js"></script>
<!-- BOOTSTRAP JS -->
<script src="<?php echo ASSETS_JS_URL; ?>bootstrap/bootstrap.min.js"></script>
<!-- CUSTOM NOTIFICATION -->
<script src="<?php echo ASSETS_JS_URL; ?>notification/SmartNotification.min.js"></script>
<!-- FORM VALIDATION PLUGINS -->
<script src="<?php echo ASSETS_JS_URL; ?>plugin/formsavior/jquery.formSavior.min.js"></script>
<script src="<?php echo ASSETS_JS_URL; ?>plugin/formvalidation/formValidation.min.js"></script>
<script src="<?php echo ASSETS_JS_URL; ?>plugin/formvalidation/framework/bootstrap.min.js"></script>
<!-- MASK INPUT PLUGINS -->
<script src="<?php echo ASSETS_JS_URL; ?>plugin/maskmoney/jquery.maskMoney.min.js"></script>
<script src="<?php echo ASSETS_JS_URL; ?>plugin/masked-input/jquery.maskedinput.min.js"></script>
<!-- BOOTSTRAP PLUGINS -->
<script src="<?php echo ASSETS_JS_URL; ?>plugin/moment/moment.min.js"></script>
<script src="<?php echo (($this->getData('kier') == true) ? ASSETS_JS_URL.'plugin/bootstrap-datepicker/bootstrap-datepicker-mason.js' :  ASSETS_JS_URL.'plugin/bootstrap-datepicker/bootstrap-datepicker.min.js' );?>"></script>
<script src="<?php echo ASSETS_JS_URL; ?>plugin/bootstrap-daterangepicker/bootstrap-daterangepicker.min.js"></script>
<script src="<?php echo ASSETS_JS_URL; ?>plugin/bootstrap-select/bootstrap-select.min.js"></script>
<script src="<?php echo ASSETS_JS_URL; ?>plugin/bootstrap-multiselect/bootstrap-multiselect.min.js"></script>
<script src="<?php echo ASSETS_JS_URL; ?>plugin/bootstrap-tags/bootstrap-tagsinput.min.js"></script>
<!--<script src="<?php echo ASSETS_JS_URL; ?>plugin/bootstrap-session-timeout/bootstrap-session-timeout.min.js"></script>
<script type="text/javascript">$.sessionTimeout();</script>-->
<?php $jsIncludes = $this->getIncludes('jsInclude'); if(!empty($jsIncludes)) { echo $jsIncludes; } ?>
<!-- browser msie issue fix -->
<script src="<?php echo ASSETS_JS_URL; ?>plugin/msie-fix/jquery.mb.browser.min.js"></script>
<!-- MAIN APP JS FILE -->
<script src="<?php echo ASSETS_JS_URL; ?>app.modified.min.js"></script>
<script type="text/javascript">$(document).ready(function() { pageSetUp(); })</script>
<script src="<?php echo ASSETS_JS_URL; ?>jquery-site.js"></script>
<?php $footerCode = $this->getIncludes('footerCode'); if(!empty($footerCode)) { echo $footerCode; } ?>