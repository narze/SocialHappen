<div>
	<h1><?php echo $action_data['data']['checkin_welcome_message']; ?> <?php echo $user['user_first_name']?></h1>
	<div>
		<form name="" action="<?php echo base_url('actions/checkin/add_user_data_checkin'); ?>" method="post">
			<div><?php echo $action_data['data']['checkin_challenge_message']; ?></div>
			<div>
				<?php echo $action_data['data']['checkin_facebook_place_name']; ?>
				TO-DO selection
				<input type="text" name="facebook_place_id" />
			</div>
		
			<input type="hidden" name="action_data_hash" value="<?php echo $action_data['hash']; ?>" />
			<div>
				<input type="submit" value="submit" />
		</form>
	</div>
</div>