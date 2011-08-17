 <ul>
	<?php if(isset($campaigns)) :
		foreach($campaigns as $campaign): ?>
		<li>
            <div>
              <p class="pic"><img src="<?php echo imgsize($campaign['campaign_image'],'normal');?>" alt="" /></p>
              <h2><?php echo $campaign['campaign_name'];?></h2>
              <p><?php echo $campaign['campaign_detail'];?></p>
              <p class="link"><a href="#">read more</a></p>
            </div>
            <div>
				<h2>Remaining Time</h2>
					<?php if(date('Y-m-d H:i:s') < $campaign['campaign_end_timestamp'] ) { ?>
						<div style="display: none;" class="campaign-end-time"><?php echo $campaign['campaign_end_timestamp'];?></div>
						<?php if(!$is_admin) { ?>
							<p><a class="bt-join" href="#"><span>Join</span></a></p>
						<?php } ?> 
					<?php } else { ?>
					<p><a class="bt-time_up"><span>Time's up</span></a></p>
					<?php } ?> 
            </div>
          </li>
		<?php endforeach; 
		endif;?>
	  
		<?php if(isset($apps)) :
		foreach($apps as $app): ?>
		<li>
            <div>
              <p class="pic"><img src="<?php echo imgsize($app['app_image'],'normal');?>" alt="" /></p>
              <h2><?php echo $app['app_name'];?></h2>
              <p><?php echo $app['app_description'];?></p>
              <p class="link"><a href="#">read more</a></p>
            </div>
            <?php if($is_admin) :?>			
			<div class="clear">
              <ul>
                <li><?php echo '[]';?><span>Today</span></li>
                <li><?php echo '[]';?><span>All</span></li>
              </ul>
            </div>
			<?php endif; ?>   
           </li>
		<?php endforeach; 
		endif;?>
</ul>