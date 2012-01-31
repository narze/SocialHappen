<?php if(!issetor($reward_item_id)){
	$id = $reward_item['_id']; $reward_item_id = $id->{'$id'};
}?>
<div class="reward-item" data-item-id="<?php echo $reward_item_id;?>">
	<div>Image : <img src="<?php echo $reward_item['image'];?>" /></div>
	<div>Name : <span><?php echo $reward_item['name']?></span></div>
	<div>Status : <span><?php echo $reward_item['status']?></span></div>
	<div>Point required : <span><?php echo $reward_item['redeem']['point']?></span></div>
	<div>Amount : <span><?php echo $reward_item['redeem']['amount']?></span></div>
	<div>Value : <span><?php echo $reward_item['value']?></span></div>
	<div>Description : <span><?php echo $reward_item['description']?></span></div>
	<a class="remove-reward-item">Remove</a>
	<a class="edit-reward-item">Edit</a>
</div>