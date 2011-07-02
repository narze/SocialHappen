<h1>Select company</h1>
<h3>Or <?php echo anchor('/home/create_company', 'Create company',array('id'=>'create_company'));?></h3>
<ul>
	<?php foreach($user_companies as $company) : 
		$count =  array_merge(json_decode(file_get_contents(base_url()."company/json_get_pages_count/{$company['company_id']}"),TRUE),
		json_decode(file_get_contents(base_url()."company/json_get_installed_apps_count/{$company['company_id']}"),TRUE),
		json_decode(file_get_contents(base_url()."company/json_get_campaigns_count/{$company['company_id']}"),TRUE));
	
	?>
		<li>
			<a href="<?php echo base_url()."company/{$company['company_id']}";?>">
				<span><img src="<?php echo imgsize($company['company_image'],'square');?>" /></span>
				<span>
					<p><?php echo $company['company_name'];?></p>
					<p>Pages (<span><?php echo $count['page_count'];?></span>)
					, Apps (<span><?php echo $count['app_count'];?></span>)
					, Campaigns(<span><?php echo $count['campaign_count'];?></span>)</p>
				</span>
			</a>
		</li>
	<?php endforeach; ?>
</ul>