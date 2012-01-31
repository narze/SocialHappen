<?php if(!issetor($reward_item_id)){
	$id = $reward_item['_id']; $reward_item_id = $id->{'$id'};
}?>
<div class="reward-item" data-item-id="<?php echo $reward_item_id;?>">
	<div class="section first">
		<div class="item-image" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);"></div>
		<ul class="item-info">
			<li>
				<p><span class="tc-green6 bold">Quanity: </span><?php echo $reward_item['redeem']['amount']?></p>
				<p><span class="tc-green6 bold">Value: </span><?php echo $reward_item['value']?></p>
				<p><span class="tc-green6 bold">Required point: </span><span class="point fs14"><?php echo $reward_item['redeem']['point']?></span></p>
			</li>
			<li>
				<span class="tc-green6 bold">Status : </span><span class="fs16 fr bold<?php echo $reward_item['status']=='draft' ? '' : ' tc-green6'; ?>"><?php echo ucfirst($reward_item['status']); ?></span>
			</li>
			<li class="ta-center">
				<div class="tc-green6 bold">Remaining Time</div>
				<div class="remaining-time bold fs16 tc-grey5"><?php echo $reward_item['end_timestamp']; ?></div>
				<div class="fs11">(<?php echo date('j M Y', $reward_item['start_timestamp']) .' - '. date('j M Y', $reward_item['end_timestamp']); ?>)</div>
			</li>
		</ul>
	</div>
	<div class="section bd0 p15 mb10">
		<div class="tc-green6 fs16 bold"><?php echo $reward_item['name']?></div>
		<div><?php echo nl2br($reward_item['description']);?></div>
	</div>
	<div class="ta-right mb10 p10 buttons">
		<a class="remove-reward-item btn red"><span>Remove</span></a>
		<a class="edit-reward-item btn green"><span>Edit</span></a>
	</div>
</div>