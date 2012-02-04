<div class="popup-fb">
	<?php if($success) :?>
		<h2>Get this reward</h2>
		<div class="ta-center mb20"><span class="fs18 tc-green6 bold">Congratulation!</span><br>This reward is yours!</div>
		<div class="reward-item">
			<div class="section first">
				<div class="item-image" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
				</div>
				<ul class="item-info">
					<li class="box">
						<p><span class="tc-green6 bold">Value: </span><?php echo $reward_item['value']?></p>
					</li>
					<li class="box">
						<span class="tc-green6 bold">User who got this reward : </span><?php 
						if ($reward_item['user_list']) {
							foreach ($reward_item['user_list'] as $user) { ?>
								<a href="#<?php echo $user['user_id']; ?>" title="<?php echo $user['user_name']; ?>" class="user-thumb s25 inline-block mb10" style="background-image:url(<?php echo $user['user_image'] ? $user['user_image'] : base_url().'assets/images/default/user.png'; ?>);"></a><?php
							}
						} else { ?>
							<p>No one have got this item, be the first one!</p>
						<?php } ?>
					</li>
				</ul>
			</div>
			<div class="section bd0 mb10">
				<div class="tc-green6 fs16 bold"><?php echo $reward_item['name']?></div>
				<div class="description"><?php echo nl2br($reward_item['description']);?></div>
			</div>
		</div>
		<a href="" class="btn blue share-to-fb"><span>Sahre to your wall</span></a>
		<a href="" class="btn green fr close"><span>Close</span></a>
	<?php else : ?>
		<div class="notice error">Redeem Fail</div>
	<?php endif; ?>
</div>