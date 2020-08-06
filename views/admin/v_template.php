<?php include_once(INCLUDES_ROOT.'admin/header.php'); ?>
<?php include_once(INCLUDES_ROOT.'admin/nav.php'); ?>
<div id="main" role="main">
	<?php include_once(INCLUDES_ROOT.'admin/ribbon.php'); ?>
	<?php
		$alerts = $this->getAlerts();		
		echo '<div class="col-sm-12">';
		if(!empty($alerts)) { if(!is_array($alerts)) { $alerts = array($alerts); } } 
		if(!empty($alerts)) { foreach($alerts as $alert) { echo $alert; } }
		echo '</div>';
	?> 
   	<div id="content">
		<?php echo $content; ?>
	</div>
</div>
<?php include_once(INCLUDES_ROOT.'admin/footer.php'); ?>
