<ul>
	<li class="account-setting">Account setting</li>
	<li class="company-page-setting">Company/Page setting</li>
	<?php foreach($user_companies as $company) : ?>
		<li class="user-company"><a href="<?php echo base_url()."settings/company/{$company['company_id']}";?>"><?php echo "Company {$company['company_name']}"; ?></a></li>
		<?php foreach($company_pages[$company['company_id']] as $page) : ?>
			<li class="company-page"><a href="<?php echo base_url()."settings/page/{$page['page_id']}";?>"><?php echo "Page {$page['page_name']}"; ?></a></li>
		<?php endforeach;
	endforeach;
	?>
	<li>Package/Billing information</li>
	<li>Reference</li>
</ul>