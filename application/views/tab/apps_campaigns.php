<ul><?php 
	if(isset($campaigns)) :
		foreach($campaigns as $campaign): ?>
			<li>
			<div>
				<p class="pic"><img class="campaign-image" alt="<?php echo $campaign['campaign_name'];?>" src="<?php echo $campaign['campaign_image'] ? imgsize($campaign['campaign_image'],'normal') : base_url().'assets/images/default/campaign.png';?>" onerror="failsafeImg(this)" /></p>
				<h2><?php echo $campaign['campaign_name'];?></h2>
				<p><?php echo $campaign['campaign_detail'];?></p>
				<p class="link"><a href="#">read more</a></p>
			</div>
			<div><?php 
					if (date('Y-m-d H:i:s') < $campaign['campaign_end_timestamp'] ) : ?>
						<h2>Remaining Time</h2>
						<div class="end-time-countdown bold mb5"><?php echo $campaign['campaign_end_timestamp'];?></div><?php 
						if($is_user) : ?>
							<p class="mb5"><a class="bt-join" href="#"><span>Join</span></a></p><?php 
						endif;
					else : ?>
						<p class="mb5"><a class="bt-time_up"><span>Time's up</span></a></p><?php 
					endif;
					
					if ($is_admin) :?>			
						<ul>
						<li><?php //echo $campaign['campaign_active_member'];?><span>Today</span></li>
						<li><?php //echo $campaign['campaign_all_member']; ?><span>All</span></li>
						</ul><?php 
					endif; ?>	
			</div>			
			</li><?php 
		endforeach; 
	endif;?>
</ul>