 <ul><?php 
	if(isset($campaigns)) :
		foreach($campaigns as $campaign): ?>
		  <li>
            <div>
              <p class="pic"><img class="campaign-image" src="<?php echo imgsize($campaign['campaign_image'],'normal');?>" alt="" /></p>
              <h2><?php echo $campaign['campaign_name'];?></h2>
              <p><?php echo $campaign['campaign_detail'];?></p>
              <p class="link"><a href="#">read more</a></p>
            </div>
            <div><?php 
					if (date('Y-m-d H:i:s') < $campaign['campaign_end_timestamp'] ) : ?>
						<h2>Remaining Time</h2>
						<div style="display: none;" class="end-time-countdown bold"><?php echo $campaign['campaign_end_timestamp'];?></div><?php 
						if($is_user) : ?>
							<p><a class="bt-join" href="#"><span>Join</span></a></p><?php 
						endif;
					else : ?>
						<p><a class="bt-time_up"><span>Time's up</span></a></p><?php 
					endif;
					
					if ($is_admin) :?>			
					  <ul>
						<li><?php echo $campaign['campaign_active_member'];?><span>Today</span></li>
						<li><?php echo $campaign['campaign_all_member']; ?><span>All</span></li>
					  </ul><?php 
					endif; ?>  
            </div>			
          </li><?php 
		endforeach; 
	endif;?>
	  
		<?php if(isset($apps)) :
		foreach($apps as $app): ?>
		<li>
            <div>
              <p class="pic"><img class="app-image" src="<?php echo imgsize($app['app_image'],'normal');?>" alt="" /></p>
              <h2><?php echo $app['app_name'];?></h2>
              <p><?php echo $app['app_description'];?></p>
              <p class="link"><a href="#">read more</a></p>
            </div>
            <?php if($is_admin) :?>			
			<div class="clear">
              <ul>
                <li><?php echo '0';?><span>Today</span></li>
                <li><?php echo '0';?><span>All</span></li>
              </ul>
            </div>
			<?php endif; ?>   
           </li>
		<?php endforeach; 
		endif;?>
</ul>