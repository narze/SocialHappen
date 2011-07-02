<?php foreach($user_companies as $company) : ?>
		<li class="user-company-setting"><a href="<?php echo base_url()."settings?s=company&id={$company['company_id']}";?>"><?php echo "Company {$company['company_name']}"; ?></a></li>
		<?php foreach($company_pages[$company['company_id']] as $page) : ?>
			<li class="company-page-setting"><a href="<?php echo base_url()."settings?s=page&id={$page['page_id']}";?>"><?php echo "Page {$page['page_name']}"; ?></a></li>
		<?php endforeach;
	endforeach;
	?>