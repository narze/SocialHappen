<?php if($reward_item_point_remain >= 0) : ?>
	You have <?php echo $page_score; ?> points left. If you redeem this item (<?php echo $reward_item_point ?>), you will have <?php echo $page_score - $reward_item_point;?> points left.
<?php else : ?>
	You have <?php echo $page_score; ?> points left. You cannot redeem this item (<?php echo $reward_item_point ?>), you should have <?php echo $reward_item_point - $page_score;?> more points.
<?php endif; ?>
<?php if($terms_and_conditions) : ;?>
<div>Terms & Conditions</div>
<div><?php echo $terms_and_conditions;?></div>
<?php endif; ?>
<a href="<?php echo base_url().'tab/redeem_reward_confirm/'.$page_id.'/'.$reward_item_id;?>">Confirm Redeem</a>