<div class="popup-fb">
	<h2>Get this reward</h2>
	<div class="reward-item">
		<div class="section first">
			<div class="item-image" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
				<div class="remaining-time abs-b bold tc-blue1">Remaining Time <div class="end-time-countdown bold tc-grey5 fs14"><?php echo $reward_item['end_timestamp_local']; ?></div></div>
			</div>
			<div class="item-info">
				<div>
					<div class="tc-green6 fs16 bold mb5"><?php echo $reward_item['name']?></div>
					<div class="description"><?php echo nl2br($reward_item['description']);?></div>
				</div>
				<div class="box">
					<p><span class="tc-green6 bold">Quanity: </span><?php echo number_format($reward_item['redeem']['amount_remain']).'/'.number_format($reward_item['redeem']['amount']);?></p>
					<p><span class="tc-green6 bold">Value: </span><?php echo $reward_item['value']?></p>
				</div>
				<div class="box">
					<div class="tc-green6 bold">User who got this reward : </div><?php 
					if ($reward_item['user_list']) {
						foreach ($reward_item['user_list'] as $user) { ?>
						<a href="#<?php echo $user['user_id']; ?>" title="<?php echo $user['user_name']; ?>" class="user-thumb s25 inline-block mb10" style="background-image:url(<?php echo $user['user_image'] ? $user['user_image'] : base_url().'assets/images/default/user.png'; ?>);"></a><?php
						}
					} else { ?>
						<p>No one have got this item, be the first one!</p>
					<?php } ?>
				</div>
			</div>
		</div>
	</div><?php 

	if($reward_item['reward_status'] == 'active') 
	{ ?>
		<div class="point-cal">
			<div class="point-summary bold fs14 lh18 round6">
				<p><span>Your Point </span><span class="fr"><?php echo number_format($page_score); ?> Points</span></p>
				<p class="tc-red">Point for this Reward <span class="fr"><?php echo number_format($reward_item_point); ?> Points</span></p>
				<?php if($reward_item_point_remain >= 0) { ?>
					<p class="tc-green6">Remaining point <span class="fr"><?php echo number_format($reward_item_point_remain);?> Points</span></p>
				<?php } ?>
			</div>

			<div class="mt20"><?php 
				if($redeem_button) 
				{ ?>
					<div class="hr mb20"></div>
					<div class="ta-center mb10">
						<a class="btn green large get-this-reward"><span>Get this reward</span></a>
					</div><?php 
				} 
				else 
				{ ?>
					<p class="ta-center tc-red bold mb20">"Your point is insufficient"</p>
					<div class="hr mb20"></div>
					<div class="mb10">
						<a class="btn grey large you-need-more-point"><span>You Need More Point</span></a>
						<a href="#" class="tc-green6 mt10 fr">How to get it?</a>
					</div><?php
				} ?>
			</div>

		</div>

		<div class="terms-and-conditions-box" style="display:none">
			<div class="hr mb20"></div>
			<h3 class="">Terms &amp; Conditions</h3>
			<div class="mt20 mb20"><?php 
				echo issetor($terms_and_conditions,'-'); ?>
				<div class="mt20 bold ta-center">
					<label><input type="checkbox" name="agree-term" class="mr5"> Accept this terms ans conditions</label>
				</div>
			</div>

			<div class="hr mb20"></div>
			<div class="ta-center mb10">
				<a href="<?php echo base_url().'tab/redeem_reward_confirm/'.$page_id.'/'.$reward_item_id;?>" class="btn inactive large confirm-get-this-reward"><span>Confirm to get this reward</span></a>
			</div>
		</div><?php 
	} 
	else 
	{ ?>
		<div class="hr mb20"></div>
		<div class="ta-center mb10">
			<a href="#" class="btn grey large ml5 cancel"><span>Close</span></a>
		</div><?php 
	} ?>
</div>