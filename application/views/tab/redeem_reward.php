<div class="popup-fb">
	<h2>Get this reward</h2>
	<div class="reward-item">
		<div class="section first">
			<div class="item-image" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
				<div class="remaining-time abs-b bold tc-blue1">Remaining Time <div class="end-time-countdown bold tc-grey5 fs16"><?php echo $reward_item['end_timestamp_local']; ?></div></div>
			</div>
			<ul class="item-info">
				<li class="box">
					<p><span class="tc-green6 bold">Quanity: </span><?php echo $reward_item['redeem']['amount_remain'].'/'.$reward_item['redeem']['amount']?></p>
					<p><span class="tc-green6 bold">Value: </span><?php echo $reward_item['value']?></p>
				</li>
				<li class="box">
					
					<p><span class="tc-green6 bold">Required point: </span><span class="point fs14"><?php echo $reward_item['redeem']['point']?></span></p>
					<p><span class="tc-green6 bold">Your point: </span><span class="point fs14"><?php echo $page_score; ?></span></p>
					<?php if($reward_item_point_remain >= 0) : ?>
						<p><span class="tc-green6 bold">Remaining point: </span><span class="point fs14"><?php echo $page_score - $reward_item_point;?></span></p>
					<?php else : ?>
						<p><span class="tc-green6 bold">Need more: </span><span class="point fs14"><?php echo $reward_item_point - $page_score;?></span></p>
					<?php endif; ?>
				</li>
			</ul>
		</div>
		<div class="section bd0 mb10">
			<div class="tc-green6 fs16 bold"><?php echo $reward_item['name']?></div>
			<div class="description"><?php echo nl2br($reward_item['description']);?></div>
		</div>
	</div>

	<?php if($terms_and_conditions) : ;?>
		<div>Terms &amp; Conditions</div>
		<div><?php echo $terms_and_conditions;?></div>
	<?php endif; ?>
	
	<div class="ta-center">
		<a href="<?php echo base_url().'tab/redeem_reward_confirm/'.$page_id.'/'.$reward_item_id;?>" class="btn green large confirm-get-this-reward"><span>Get this reward</span></a>
		<a href="#" class="btn grey large ml5 cancel"><span>Cancel</span></a>
	</div>
	
</div>