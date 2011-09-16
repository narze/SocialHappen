<div class="box-information">
        <div class="details">
          <div class="pic">
            <p><img class="user-image" src="<?php echo imgsize(issetor($user_profile['user_image']),'large'); ?>" alt=""></p>
            <p><a class="bt-go_user" href="#"><span>Goto SH Profile</span></a></p>
            <p><a class="bt-go_user" href="#"><span>Goto FB Profile</span></a></p>
          </div>
          <h2><?php echo issetor($user_profile['user_first_name']).' '.issetor($user_profile['user_last_name']); ?></h2>
          <p><?php echo issetor($user_profile['user_email']); ?></p>
        </div>
        <div class="information">
          <h2>Information</h2>
          <ul>
            <li><span>Star point</span>12</li>
            <li><span>Happy point</span>1335</li>
            <li><span>Friends</span>232</li>
            <li><span>Joined apps</span>5</li>
            <li><span>Joined campaigns</span>12</li>
          </ul>
        </div>
</div>