<div class="popup-fb">
	<h2>Get this reward</h2>
	<div class="reward-item">
		<div class="section first">
			<div class="item-image" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
				<div class="remaining-time abs-b bold tc-blue1">Remaining Time <div class="end-time-countdown bold tc-grey5 fs16"><?php echo $reward_item['end_timestamp_local']; ?></div></div>
			</div>
			<ul class="item-info">
				<li class="box">
					<p><span class="tc-green6 bold">Quanity: </span><?php echo number_format($reward_item['redeem']['amount_remain']).'/'.number_format($reward_item['redeem']['amount']);?></p>
					<p><span class="tc-green6 bold">Value: </span><?php echo $reward_item['value']?></p>
				</li>
				<li class="box">
					<p class="<?php echo $reward_item_point > $page_score ? 'tc-red' : 'tc-green6'; ?>"><span class="bold">Required point: </span><span class="point fs14"><?php echo number_format($reward_item_point); ?></span></p>
					<p><span class="tc-green6 bold">Your point: </span><span class="point fs14"><?php echo number_format($page_score); ?></span></p>
					<?php if($reward_item_point_remain >= 0) : ?>
						<p><span class="tc-green6 bold">Remaining point: </span><span class="point fs14"><?php echo number_format($reward_item_point_remain);?></span></p>
					<?php endif; ?>
				</li>
			</ul>
		</div>
		<div class="section bd0 mb10">
			<div class="tc-green6 fs16 bold"><?php echo $reward_item['name']?></div>
			<div class="description"><?php echo nl2br($reward_item['description']);?></div>
		</div>
	</div>

	<div class="hr mb20"></div>

	<?php if($terms_and_conditions) { ?>
		<div class="terms-and-conditions-box round6 p20 mb20">
			<h3 class="mt20">Terms &amp; Conditions</h3>
			<div class="mt20 mb20">
				<?php echo $terms_and_conditions;?>
				<div class="mt20 bold ta-center">
					<label><input type="checkbox" name="agree-term" class="mr5"> Accept this terms ans conditions</label>
				</div>
			</div>
		</div>
	<?php } ?>
	
	<div class="ta-center">
		<a href="<?php echo base_url().'tab/redeem_reward_confirm/'.$page_id.'/'.$reward_item_id;?>" class="btn green large confirm-get-this-reward"><span>Get this reward</span></a>
		<a href="#" class="btn grey large ml5 cancel"><span>Cancel</span></a>
	</div>
	
</div>