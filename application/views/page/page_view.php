{header}
{company_image_and_name}
{breadcrumb}
<div class="wrapper-content" id="page-dashboard">
	<div>
		{page_profile}
		<?php 
		if($page_installed == 0) 
		{ ?>
			<div class="wrapper-details">
				<div class="notice warning">				
					<div class="goto-facebook">
						<h2 class="in-popup">Page installation : One step more</h2>
						<p><b>Please, go to facebook to complete the action.</b></p>
						<?php if($facebook_tab_url) : ?>
							<a class="bt-go-facebook" href="<?php echo $facebook_tab_url; ?>" >Go to facebook</a>
						<?php else : ?>
							<a class="bt-go-facebook" href="<?php echo base_url()."tab/facebook_page/{$page_id}"; ?>" >Go to facebook</a>
						<?php endif; ?>
					</div>
				</div><br />
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