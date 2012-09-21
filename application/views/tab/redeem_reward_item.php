<?php if(!issetor($reward_item_id)){
	$id = $reward_item['_id']; $reward_item_id = $id->{'$id'};
} ?>
<div class="reward-item" data-item-id="<?php echo $reward_item['_id'];?>">
	<div class="section first">
		<div class="item-image <?php echo $reward_item['reward_status']; ?>" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
			<div class="item-overlay ta-center">
				<a href="<?php echo base_url().'tab/redeem_reward/'.$page_id.'/'.$id;?>" class="btn green large view-reward-detail"><span>View more detail</span></a>
			</div>
			<div class="remaining-time abs-b bold tc-blue1"><?php
				if($reward_item['reward_status']=='soon') { ?>
				Available in <span class="end-time-countdown tc-grey5 fr"><?php echo $reward_item['start_timestamp_local']; ?></span><?php }
				else { ?>
				Remaining Time <span class="end-time-countdown tc-grey5 fr"><?php echo $reward_item['end_timestamp_local']; ?></span><?php } ?>
			</div>
		</div>
		<ul class="item-info">
			<li>
				<div class="tc-green6 fs16 bold mb5"><?php echo $reward_item['name']?></div>
				<div class="description"><?php echo nl2br($reward_item['description']);?></div>
			</li>
			<li class="box">
				<p><span class="tc-green6 bold">Quanity: </span><?php echo umber_format($reward_item['redeem']['amount'] - $reward_item['redeem']['amount_redeemed']).'/'.number_format($reward_item['redeem']['amount']);?></p>
				<p><span class="tc-green6 bold">Value: </span><?php echo number_format($reward_item['value']).' '. $reward_currency;?></p>
			</li>
			<li class="box">
				<p><span class="tc-green6 bold">Required point: </span><span class="point fs14"><?php echo number_format($reward_item['redeem']['point']); ?></span></p>
			</li>
			<li class="box">
				<div class="tc-green6 bold">User who got this reward : </div><?php
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
		<p class="tc-blue4 bold fs14">How to get it:</p>
		<div class="tab-blue round4 bold"><span class="icon white share"></span> Share this page on your wall <span class="icon gray help fr">?</span></div>
		<div class="tab-blue round4 bold"><span class="icon white star"></span> <?php echo $reward_item['redeem']['point']?> Page Points<span class="icon gray help fr">?</span></div>
	</div>
</div>