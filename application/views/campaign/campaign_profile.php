<div class="box-information">
        <div class="details">
          <div class="pic">
            <p><img src="<?php echo $campaign_profile['campaign_image'] ? $campaign_profile['campaign_image'] : base_url().'assets/images/default/campaign.png'; ?>" alt=""></p>
          </div>
          <h2><?php echo issetor($campaign_profile['campaign_name']); ?></h2>
          <p><?php echo issetor($campaign_profile['campaign_detail']); ?></p>
          <p>
            <a class="bt-campaign_page" href="#" style="display:inline-block"><span>Goto campaign</span></a>
            <a class="bt-setting_campaign" href="<?php echo base_url()."settings/campaign/{$campaign_profile['campaign_id']}";?>" style="display:inline-block"><span>setting</span></a>
          </p>
        </div>
        <div class="information">
          <h2>Information</h2>
          <ul>
            <li><span>Status</span><?php echo issetor($campaign_profile['campaign_status']); ?></li>
            <li><span>Daily active</span><?php echo issetor($campaign_daily_active); ?></li>
            <li><span>Total member</span><?php echo issetor($campaign_total_users); ?></li>
          </ul>
        </div>
</div>