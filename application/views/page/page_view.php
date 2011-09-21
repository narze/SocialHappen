{header}
{company_image_and_name}
{breadcrumb}
<div class="wrapper-content">
	<div>
		{page_profile}
		<?php 
		if($page_installed == 0) 
		{ ?>
			<div class="wrapper-details">
				<div class="notice warning">
					<h2>Page installation : One step more</h2>
					Please, go to facebook to complete the action. <a class="gotofacebook-link" href="http://www.facebook.com/add.php?api_key=<?php echo $app_facebook_api_key; ?>&pages=1&page=<?php echo $facebook_page_id; ?>" >Go to facebook</a>
				</div>
				<br />
			</div><?php 
		} 
		else 
		{ ?>
			{page_tabs}
			{page_apps}
			{page_campaigns}
			{page_users}
			{page_report}<?php 
		} ?>
	</div>
	<div class="bottom"><!--bottom--></div>
</div>
{footer}