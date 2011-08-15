	<div class="popup_select-company">
    <h2>Select company</h2>
	<p>Or <?php echo anchor('/home/create_company', 'Create company',array('id'=>'create_company'));?></p>
    <ul>
		<?php foreach($user_companies as $company) : 
			$count =  array_merge(json_decode(file_get_contents(base_url()."company/json_get_pages_count/{$company['company_id']}"),TRUE),
			json_decode(file_get_contents(base_url()."company/json_get_installed_apps_count/{$company['company_id']}"),TRUE),
			json_decode(file_get_contents(base_url()."company/json_get_campaigns_count/{$company['company_id']}"),TRUE));
		
		?>
      <li>
        <div class="detail-list">
          <h2><a href="<?php echo base_url()."company/{$company['company_id']}";?>"><?php echo $company['company_name'];?></a></h2>
          <p>Pages (<?php echo $count['page_count'];?>) , Apps (<?php echo $count['app_count'];?>) , Campaigns(<?php echo $count['campaign_count'];?>)</p>
          <p class="thumb">
			<a href="<?php echo base_url()."company/{$company['company_id']}";?>">
				<img src="<?php echo imgsize($company['company_image'],'square');?>" alt="" />
			</a>
		  </p>
        </div>
      </li>
		<?php endforeach; ?>
    </ul>
  </div>