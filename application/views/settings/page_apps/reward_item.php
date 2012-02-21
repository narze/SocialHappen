<?php 
	if(!issetor($reward_item_id)){
		$id = $reward_item['_id']; $reward_item_id = $id->{'$id'};
	}
?>
<div class="reward-item <?php echo $reward_item['status']; ?>" data-item-id="<?php echo $reward_item_id;?>">
	<div class="section first">
		<div class="item-image <?php echo $reward_item['reward_status']; ?>" style="background-image:url(<?php echo $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
			<div class="remaining-time abs-b bold tc-blue1"><?php
				if($reward_item['reward_status']=='soon') { ?>
				Available in <span class="end-time-countdown tc-grey5 fr"><?php echo $reward_item['start_date']; ?></span></div><?php }
				else { ?>
				Remaining Time <span class="end-time-countdown tc-grey5 fr"><?php echo $reward_item['end_date']; ?></span></div><?php } ?>
		</div>
		<ul class="item-info">
			<li>
				<div class="tc-green6 fs16 bold mb5"><?php echo $reward_item['name']?></div>
				<div class="description"><?php echo nl2br($reward_item['description']);?></div>
			</li>
			<li class="box">
				<p><span class="tc-green6 bold">Quanity: </span><?php echo number_format($reward_item['redeem']['amount']);?></p>
				<p><span class="tc-green6 bold">Value: </span><?php echo number_format($reward_item['value']);?></p>
			</li>
			<li class="box">
				<p><span class="tc-green6 bold">Required point: </span><span class="point fs14"><?php echo number_format($reward_item['redeem']['point']);?></span></p>
			</li>
			<li class="box">
				<p><span class="tc-green6 bold">From :</span><span class="fr fs11"><?php echo date('j F Y, h.iA',strtotime($reward_item['start_date'])); ?></span></p>
				<p><span class="tc-green6 bold">To :</span> <span class="fr fs11"><?php echo date('j F Y, h.iA',strtotime($reward_item['end_date'])); ?></span></p>
			</li>
			<li class="box">
				<div class="tc-green6 bold">Status : <span class="fs15 fr bold<?php echo $reward_item['status']=='draft' ? ' tc-grey5' : ' tc-green6'; ?>"><?php echo ucfirst($reward_item['status']); ?></span></div>
			</li>
		</ul>
	</div>
	<div class="section bd0 p15 mb10">
		<p class="tc-blue4 bold fs14">How to get it:</p>
		<div class="tab-blue round4 bold"><span class="icon white share"></span> Share this page on your wall <span class="icon gray help fr">?</span></div>
		<div class="tab-blue round4 bold"><span class="icon white star"></span> <?php echo $reward_item['redeem']['point']?> Page Points<span class="icon gray help fr">?</span></div>
	</div>
	<div class="ta-right mb10 p10 buttons">
		<a class="remove-reward-item btn red"><span>Remove</span></a>
		<a class="edit-reward-item btn green"><span>Edit</span></a>
	</div>
</div>