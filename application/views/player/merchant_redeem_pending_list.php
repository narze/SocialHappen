<?php if(!$redeeming_users) {
	echo 'No pending redeeming user';
} else {
	foreach($redeeming_users as $user) : ?>
		<div>
			<?php foreach($user['challenge_redeeming'] as $challenge_id) : ?>
				<?php echo anchor('player/merchant_redeem_pending/'.$user['user_id'].'/'.$challenge_id);
				echo '<br />'; ?>
			<?php endforeach; ?>
		</div>
	<?php endforeach; 
}?>