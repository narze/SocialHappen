<div class="box-information">
        <div class="details">
          <div class="pic">
            <p><img src="<?php echo issetor($campaign_profile['campaign_image']); ?>" alt=""></p>
            <p><a class="bt-go_campaign" href="#"><span>Goto campaign</span></a></p>
            <p><a class="bt-setting" href="<?php echo base_url()."o_setting?s=campaign&id={$campaign_profile['campaign_id']}";?>"><span>setting</span></a></p>
          </div>
          <h2><?php echo issetor($campaign_profile['campaign_name']); ?></h2>
          <p><?php echo issetor($campaign_profile['campaign_detail']); ?></p>
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