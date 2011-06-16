<ul>
	<li class="account-setting"><a href="<?php echo base_url()."settings/account/{$user['user_id']}";?>">Account setting</a></li>
	<li class="company-page-list"><a href="<?php echo base_url()."settings/company_pages/{$user['user_id']}";?>">Company/Page setting</a></li>
	<?php foreach($user_companies as $company) : ?>
		<li class="user-company-setting"><a href="<?php echo base_url()."settings/company/{$company['company_id']}";?>"><?php echo "Company {$company['company_name']}"; ?></a></li>
		<?php foreach($company_pages[$company['company_id']] as $page) : ?>
			<li class="company-page-setting"><a href="<?php echo base_url()."settings/page/{$page['page_id']}";?>"><?php echo "Page {$page['page_name']}"; ?></a></li>
		<?php endforeach;
	endforeach;
	?>
	<li>Package/Billing information</li>
	<li>Reference</li>
</ul>
<hr />