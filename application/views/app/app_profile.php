<div class="box-information">
        <div class="details">
          <div class="pic">
            <p><img src="<?php echo issetor($app_profile['app_image']); ?>" alt=""></p>
            <p><a class="bt-go_page" href="#"><span>Uninstall</span></a></p>
            <p><a class="bt-add_app" href="#"><span>New campaign</span></a></p>
          </div>
          <h2><?php echo issetor($app_profile['app_name']); ?></h2>
          <p><?php echo issetor($app_profile['app_detail']); ?></p>
        </div>
        <div class="information">
          <h2>Information</h2>
          <ul>
            <li><span>New Member</span><?php echo issetor($app_profile['new_users']); ?></li>
            <li><span>All Member</span><?php echo issetor($app_profile['all_users']); ?></li>
            <li><span>Installed on</span><?php echo issetor($count_installed_on);?></li>
          </ul>
        </div>
</div>