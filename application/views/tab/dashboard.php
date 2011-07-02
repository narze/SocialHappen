<div id="profile">
	<div id="image">
	</div>
	<div id="name">
	</div>
	<div id="points">
	<?php if($is_admin) :?>
	<a href="#">Dashboard</a>
	<?php endif; ?>
	</div>
	<?php if(!$is_liked) :?>
	<div>
	You haven't like this page yet.
	</div>
	<?php endif; ?>
	<div id="friends">
		<?php if(isset($friends)) :
			foreach($friends as $friend): ?>
			<div class="friend">
				<div class="image">
					<img class="image" src=" http://graph.facebook.com/<?php echo $friend['uid'];?>/picture" />
				</div>
				<div class="friend-detail">
					<div class="name"><?php echo $friend['name'];?></div>
				</div>
			</div>
		<?php endforeach; 
		endif;?>
	</div>
</div>
<div id="current-campaign">
	<h3>Campaigns in this page</h3>
	<?php if(isset($campaigns)) :
		foreach($campaigns as $campaign): ?>
		<div class="campaign">
			<div class="image">
				<img class="image" src="<?php echo imgsize($campaign['campaign_image'],'square');?>" />
			</div>
			<div class="campaign-detail">
				<div class="name"><?php echo $campaign['campaign_name'];?></div>
				<div class="detail"><?php echo $campaign['campaign_detail'];?></div>
				<div class="more"><a href="#">read more</a></div>
			</div>
			<div class="time">
				<?php if($is_admin) :?>
					Remaining time
				<?php else :?>
					<div class="join-button"><a href="#">Join</a></div>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; 
	endif;?>
</div>
<div id="installed-apps">
	<h3>Applications in this page</h3>
	<?php if(isset($installed_apps)) :
		foreach($installed_apps as $installed_app): ?>
		<div class="app">
			<div class="image">
				<img class="image" src="<?php echo imgsize($installed_app['app_image'],'square');?>" />
			</div>
			<div class="app-name">
				<?php echo $installed_app['app_name'];?>
			</div>
		</div>
	<?php endforeach; 
	endif;?>
</div>
<div id="recent-activities">
	<h3>Recent activities</h3>
	<div id="filter">
		Display <a href="#">All</a> | <a href="#">Applicaion</a> | <a href="#">Campaign</a>
	</div>
	<div id="activity-list">
	</div>
</div>