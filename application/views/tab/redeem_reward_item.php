<?php if(!issetor($reward_item_id)){
	$id = $reward_item['_id']; $reward_item_id = $id->{'$id'};
} ?>
<div class="reward-item" data-item-id="<?php echo $reward_item['_id'];?>">
	<div class="section first">
		<div class="item-image <?php echo $reward_item['reward_status']; ?>" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
			<div class="remaining-time abs-b bold tc-blue1">Remaining Time <span class="end-time-countdown bold tc-grey5 fr"><?php echo $reward_item['end_timestamp_local']; ?></span></div>
		</div>
		<ul class="item-info">
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
			<li class="box">
				<p><span class="tc-green6 bold">Quanity: </span><?php echo $reward_item['redeem']['amount_remain'].'/'.$reward_item['redeem']['amount']?></p>
				<p><span class="tc-green6 bold">Value: </span><?php echo $reward_item['value']?></p>
				<p><span class="tc-green6 bold">Required point: </span><span class="point fs14"><?php echo number_format($reward_item['redeem']['point']); ?></span></p>
			</li>
			<?php if($redeem_button && $reward_item['reward_status'] == 'active') { ?>
			<li>
				<a href="<?php echo base_url().'tab/redeem_reward/'.$page_id.'/'.$id;?>" class="btn green w100 large get-this-reward"><span>Get this reward</span></a>
			</li>
			<?php } ?>
		</ul>
	</div>
	<div class="section bd0 mb10">
		<div class="tc-green6 fs16 bold"><?php echo $reward_item['name']?></div>
		<div class="description"><?php echo nl2br($reward_item['description']);?></div>
	</div>
</div>