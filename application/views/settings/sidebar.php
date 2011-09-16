<div class="sidebar">
	<div class="menuleft">
		<ul>
			<li><a class="account-setting" href="<?php echo base_url()."settings?s=account&id={$user['user_id']}";?>"><b>Account setting</b></a></li>
			<li><a class="company-page-list" href="<?php echo base_url()."settings?s=company_pages&id={$user['user_id']}";?>"><b>Company / Page setting</b></a>
			<ul>
				<?php foreach($user_companies as $company) : ?>
					<li>
						<a class="user-company-setting" href="<?php echo base_url()."settings?s=company&id={$company['company_id']}";?>">
							<img class="company-image" src="<?php echo imgsize("{$company['company_image']}",'square'); ?>" /><?php echo "{$company['company_name']}"; ?>
						</a>
						<ul>
							<?php foreach($company_pages[$company['company_id']] as $page) : ?>
								<li>
									<a class="company-page-setting" href="<?php echo base_url()."settings?s=page&id={$page['page_id']}";?>">
										&raquo;<img class="page-image" src="<?php echo imgsize("{$page['page_image']}",'square'); ?>" /><?php echo "{$page['page_name']}"; ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php endforeach; ?>
			</ul>
			</li>
			<li><a class="package-billing" href="<?php echo base_url()."settings?s=package&id={$user['user_id']}";?>"><b>Package/Billing</b></a>
			<!--<li><a><b>Reference</b></a></li>-->
		</ul>
	</div>
</div>