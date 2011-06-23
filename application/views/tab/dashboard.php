<div id="profile">
	<div id="image">
	</div>
	<div id="name">
	</div>
	<div id="points">
	</div>
	<div id="friends">
	</div>
</div>
<div id="current-campaign">
	<?php if(isset($campaigns)) :
		foreach($campaigns as $campaign): ?>
		<div class="campaign">
			<div class="image">
				<img class="image" src="<?php echo $campaign['campaign_image'];?>" />
			</div>
			<div class="campaign-detail">
				<div class="name"><?php echo $campaign['campaign_name'];?></div>
				<div class="detail"><?php echo $campaign['campaign_detail'];?></div>
				<div class="more"><a href="#">read more</a></div>
			</div>
			<div class="time">
				<div class="join-button"><a href="#">Join</a></div>
			</div>
		</div>
	<?php endforeach; 
	endif;?>
</div>
<div id="apps">
</div>
<div id="recent-activities">
</div>