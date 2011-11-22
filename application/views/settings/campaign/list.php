<?php if($this->input->get('success') || $this->input->get('update_success') || $this->input->get('invite_settings_success') || $this->input->get('sharebutton_settings_success')){ ?>
        <div class="notice success">Success</div><?php
} else if ($this->input->get('error')){ ?>
        <div class="notice success">Error</div><?php
}
if($campaigns) : ?>
<ul class="campaigns-list">
 	<?php foreach($campaigns as $campaign) : ?>
		<li>
			<p>
				<?php echo '<a class="a-update-campaign" href="'.base_url().'settings/campaign/update/'.$app_install_id.'/'.$campaign['campaign_id'].'">'.$campaign['campaign_name'].'</a>'; ?>
				<?php echo $campaign['campaign_start_date'].' to '.$campaign['campaign_end_date'];?>
			</p>
			<ul class="app-components">
				<li><a class="a-component-invite" href="<?php echo base_url().'settings/app_component/invite/'.$app_install_id.'/'.$campaign['campaign_id'];?>">Invite</a></li>
				<li><a class="a-component-sharebutton" href="<?php echo base_url().'settings/app_component/sharebutton/'.$app_install_id.'/'.$campaign['campaign_id'];?>">Share button</a></li>
			</ul>
		</li>
			
	<?php endforeach; ?>
</ul>
<?php else :?>
	<div class="notice warning"> No campaign yet.</div>
<?php endif; ?>
<div><a class="a-new-campaign bt-addnew_campaign" style="margin: 10px 5px;" href="<?php echo base_url().'settings/campaign/add/'.$app_install_id; ?>">create new campaign</a></div>
<?php if($this->input->get('tab') != TRUE) : ?>
<a class="a-back-to-app-settings bt-back" data-app-install-id="<?php echo $app_install_id;?>" href="<?php echo base_url().'settings/campaign/'. $app_install_id;?>">Back</a>
<?php endif; ?>