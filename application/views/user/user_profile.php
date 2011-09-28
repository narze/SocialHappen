<div class="box-information">
        <div class="details">
          <div class="pic">
            <p><img class="user-image" src="<?php echo imgsize(issetor($user_profile['user_image']),'large'); ?>" alt=""></p>
            <p><a class="bt-go_user_profile" href="#">Goto SH Profile</a></p>
            <p><a class="bt-go_user_fb" href="#">Goto FB Profile</a></p>
          </div>
		  <div class="info">
          <h2><?php echo issetor($user_profile['user_first_name']).' '.issetor($user_profile['user_last_name']); ?></h2>
          <p><?php echo issetor($user_profile['user_email']); ?></p>
		  
		  <div class="recent-app">
			<fieldset>
				<legend>Recent Application</legend>
				<ul>
					<li>This user did not use any applications.</li>
				</ul>
			</fieldset>
		  </div>
		  </div>
		  
        </div>
        <div class="information">
          <h2>Information</h2>
          <ul>
            <li><span>Star point<div class="icon-star"></div></span>12</li>
            <li><span>Happy point<div class="icon-happy"></div></span>1335</li>
            <li><span>Friends</span>232</li>
            <?php if (isset($count['app']) && $count['app'] > 0) { ?><li><span>Joined apps</span><?php echo number_format($count['app']); } ?></li>
            <?php if (isset($count['campaigns']) && $count['campaigns'] > 0) { ?><li><span>Joined campaigns</span><?php echo number_format($count['campaigns']); } ?></li>
          </ul>
		  
		  <?php if (!isset($count['app']) || $count['app']==0 || !isset($count['campaigns']) || $count['campaigns']==0 ) { ?>
		  <div class="no-app-no-campaigns">
			<?php if (!isset($count['app']) || $count['app']==0) { ?><p>Did not use any Applications.</p><?php } ?>
			<?php if (!isset($count['campaigns']) || $count['campaigns']==0) { ?><p>Did not use any Campaigns.</p><?php } ?>
		  </div>
		  <?php } ?>
		  
        </div>
</div>