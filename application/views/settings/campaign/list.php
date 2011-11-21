<?php if($campaigns) : ?>
<ul>
 	<?php foreach($campaigns as $campaign) : ?>
		<li><?php echo '<a class="a-update-campaign" href="'.base_url().'settings/campaign/update/'.$app_install_id.'/'.$campaign['campaign_id'].'">'.$campaign['campaign_name'].'</a> '.$campaign['campaign_start_date'].' '.$campaign['campaign_end_date'];?></li>
		<li>
			<ul class="app-components">
				<li>- <a class="a-component-homepage" href="<?php echo base_url().'settings/app_component/homepage/'.$app_install_id.'/'.$campaign['campaign_id'];?>">Homepage</a></li>
				<li>- <a class="a-component-invite" href="<?php echo base_url().'settings/app_component/invite/'.$app_install_id.'/'.$campaign['campaign_id'];?>">Invite</a></li>
				<li>- <a class="a-component-sharebutton" href="<?php echo base_url().'settings/app_component/sharebutton/'.$app_install_id.'/'.$campaign['campaign_id'];?>">Share button</a></li>
			</ul>
		</li>
			
	<?php endforeach; ?>
<?php else :?>
	<div class="notice warning"> No campaign yet.</div>
<?php endif; ?>
<div><a class="a-new-campaign bt-addnew_campaign" style="margin: 10px 5px;" href="<?php echo base_url().'settings/campaign/add/'.$app_install_id; ?>">create new campaign</a></div>
<?php if($this->input->get('tab') != TRUE) : ?>
<a class="a-back-to-app-settings bt-back" data-app-install-id="<?php echo $app_install_id;?>" href="<?php echo base_url().'settings/campaign/'. $app_install_id;?>">Back</a>
<?php endif; ?>