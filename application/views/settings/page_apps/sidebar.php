<div class="sidebar">
	<div class="menuleft">
		<ul>
			<li>Platform apps
			<ul class="platform-apps">
				<li><a id="signup-fields" href="<?php echo base_url()."settings/page_signup_fields/view/{$page['page_id']}";?>"><b>Signup Form</b></a></li>
				<li><a id="badges" href="<?php echo base_url()."settings/page_badges/view/{$page['page_id']}";?>"><b>Badges</b></a></li>
			</ul>
			</li>
			<li>Page apps
			<ul class="page-apps">
				<?php foreach($page_apps as $page_app) : ?>
					<li><a class="app" data-appinstallid="<?php echo $page_app['app_install_id'];?>" href="<?php echo base_url()."settings/page_apps/view/{$page['page_id']}/{$page_app['app_install_id']}";?>"><b><?php echo $page_app['app_name'];?></b></a></li>
				<?php endforeach; ?>
			</ul>
			</li>
		</ul>
	</div>
</div>