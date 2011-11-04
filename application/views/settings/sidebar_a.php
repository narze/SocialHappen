<div class="sidebar">
	<div class="menuleft">
		<ul>
			<li><a class="account-setting" href="<?php echo base_url()."settings/account/view/{$user['user_id']}";?>"><b>Account setting</b></a></li>
			<?php if($user_companies) : ?>
				<li><a class="company-page-list" href="<?php echo base_url()."settings/company_pages/view/{$user['user_id']}";?>"><b>Company / Page setting</b></a>
					<ul>
						<?php foreach($user_companies as $company) : ?>
							<li>
								<a class="user-company-setting" href="<?php echo base_url()."settings/company/view/{$company['company_id']}";?>">
									<img class="company-image" src="<?php echo imgsize("{$company['company_image']}",'square'); ?>" /><?php echo "{$company['company_name']}"; ?>
								</a>
								<ul>
									<?php foreach($company_pages[$company['company_id']] as $page) : ?>
										<li>
											<a class="company-page-setting" href="<?php echo base_url()."settings/page/view/{$page['page_id']}";?>">
												&raquo;<img class="page-image" src="<?php echo imgsize("{$page['page_image']}",'square'); ?>" /><?php echo "{$page['page_name']}"; ?>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</li>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php endif;
			if($user_current_package_id) { ?>
				<li><a class="package-billing" href="<?php echo base_url()."settings/package/view/{$user['user_id']}";?>"><b>Package/Billing</b></a>
			<?php } ?>
			<!--<li><a><b>Reference</b></a></li>-->
		</ul>
	</div>
</div>