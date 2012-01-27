<?php if(!issetor($reward_item_id)){
	$id = $reward_item['_id']; $reward_item_id = $id->{'$id'};
}?>
<div class="reward-item" data-item-id="<?php echo $reward_item_id;?>">
	<div>Name : <span><?php echo $reward_item['name']?></span></div>
	<div>Status : <span><?php echo $reward_item['status']?></span></div>
	<div>Point required : <span><?php echo $reward_item['redeem']['point']?></span></div>
	<div>Amount : <span><?php echo $reward_item['redeem']['amount']?></span></div>
	<div>Who have got this item? 
		<?php if($reward_item['user_list']) :
			foreach($reward_item['user_list'] as $user) :?>
				<p>- 
					<?php echo $user['user_name'];
					echo '<img width="50" height="50" src="'.$user['user_image'].'" />';
					?>
				</p>
			<?php endforeach; else : ?>
			No one have got this item, be the first one!
			<?php endif; ?>
	</div>
				
					 

	<?php if($redeem_button) : ?>
		<a href="<?php echo base_url().'tab/redeem_reward/'.$page_id.'/'.$id;?>">Redeem</a>
	<?php endif; ?>
</div>