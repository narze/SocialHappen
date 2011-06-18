{header}
<h1>Select company</h1>
<h3>Or <?php echo anchor('home/create_company','create new company'); ?></h3>
<ul>
	<?php foreach($user_companies as $company) : ?>
		<li><a href="<?php echo base_url()."company/{$company['company_id']}";?>"><?php echo $company['company_name'];?></a></li>
	<?php endforeach; ?>
</ul>
{footer}