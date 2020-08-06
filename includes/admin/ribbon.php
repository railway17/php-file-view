	<?php 

	$breadcrumbs = array( "Dashboard" => ADMIN_URL.'dashboard/index.php' ); ?>

	<div id="ribbon">
		<ol class="breadcrumb">
			<?php
				if(isset($breadcrumbs)) {
					foreach ($breadcrumbs as $display => $url) {
						$breadcrumb = $url != "" ? '<a href="'.$url.'">'.$display.'</a>' : $display;
						echo '<li>'.$breadcrumb.'</li>';
					}
					echo '<li>'.$metaPageTitle.'</li>';
				}
			?>
		</ol>
	</div>