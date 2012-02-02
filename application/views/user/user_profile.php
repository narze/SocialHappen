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
			</fieldset>
			<ul class="app-icon-list<?php echo $recent_apps ? '' : ' no-app'; ?>"><?php
				if($recent_apps)
				{
					foreach($recent_apps as $app)
					{ ?>
						<li class="app-container">
							<a class="app-icon" href="<?php echo base_url().'app/'.$app['app_id']; ?>" title="<?php echo $app['app_name']; ?>" ><img class="app-image" width="64" height="64" src="<?php echo $app['app_image']; ?>" /></a>
							<a class="app-name" href="<?php echo base_url().'app/'.$app['app_id']; ?>" title="<?php echo $app['app_name']; ?>" ><?php echo $app['app_name']; ?></a>
						</li><?php
					}
				}
				else
				{ ?>
					<li class="app-container">This user did not use any applications.</li><?php
				} ?>
			</ul>
		  </div>
		  </div>
		  
        </div>
        <div class="information">
          <h2>Information</h2>
          <ul>
            <li><span>Star point<div class="icon-star-white"></div></span>12</li>
            <li><span>Happy point<div class="icon-happy-white"></div></span>1335</li>
            <li><span>Friends</span>232</li>
            <?php if (isset($count['app']) && $count['app'] > 0) { ?><li><span>Joined apps</span><?php echo number_format($count['app']); } ?></li>
            <?php if (isset($count['campaigns']) && $count['campaigns'] > 0) { ?><li><span>Joined campaigns</span><?php echo number_format($count['campaigns']); } ?></li>
          </ul>
		  
		  <div class="app-and-campaigns">
			<p><?php echo $recent_apps ? 'Joined '.count($recent_apps). ' applications' : 'Did not use any Applications.'; ?></p>
			<p><?php echo $recent_campaigns ? 'Joined '.count($recent_campaigns). ' campaigns' : 'Did not use any Campaigns.'; ?></p>
		  </div>
		  
        </div>
</div>