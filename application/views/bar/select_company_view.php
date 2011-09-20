<div class="popup_select-company">
    <h2>Select company</h2>
	<?php if($user_can_create_company) { ?>
	<p>Or <?php echo anchor('', 'Create company',array('class'=>'bt-create_company', 'style'=>'background:none;display:inline;float:none;color:#333!important;text-indent:0'));?></p>
	<?php } ?>
    <ul><?php 
	foreach($user_companies as $company) 
	{ ?>
		<li>
			<div class="detail-list">
			  <h2><a href="<?php echo base_url()."company/{$company['company_id']}";?>"><?php echo $company['company_name'];?></a></h2>
			  <p>Pages (<?php echo $company['page_count'];?>) , Apps (<?php echo $company['app_count'];?>) , Campaigns(<?php echo $company['campaign_count'];?>)</p>
			  <p class="thumb">
				<a href="<?php echo base_url()."company/{$company['company_id']}";?>">
					<img src="<?php echo imgsize($company['company_image'],'square');?>" title="<?php echo $company['company_name'];?>" />
				</a>
			  </p>
			</div>
		</li><?php 
	} ?>
    </ul>
</div>