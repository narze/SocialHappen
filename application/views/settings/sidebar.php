<div class="menuleft">
	<ul>
		<li class="account-setting"><a href="<?php echo base_url()."settings?s=account&is={$user['user_id']}";?>"><b>Account setting</b></a></li>
		<li class="company-page-list"><a href="<?php echo base_url()."settings?s=company_pages&id={$user['user_id']}";?>"><b>Company / Page setting</b></a>
		<ul>
			<?php foreach($user_companies as $company) : ?>
				<li class="user-company-setting"><a href="<?php echo base_url()."settings?s=company&id={$company['company_id']}";?>"><img src="<?php echo "{$company['company_image']}"; ?>" /><?php echo "{$company['company_name']}"; ?></a>
					<ul>
						<?php foreach($company_pages[$company['company_id']] as $page) : ?>
							<li class="company-page-setting"><a href="<?php echo base_url()."settings?s=page&id={$page['page_id']}";?>">&raquo;<img src="<?php echo "{$page['page_image']}"; ?>" /><?php echo "{$page['page_name']}"; ?></a></li>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php endforeach; ?>
		</ul>
		</li>
		<li><a><b>Package/Billing information</b></a></li>
		<li><a><b>Reference</b></a></li>
	</ul>
</div>