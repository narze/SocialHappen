<div class="well">
	<h1><?php echo $action_data['data']['feedback_welcome_message']; ?> <?php echo $user['user_first_name']?> <?php echo $user['user_last_name']?> <img src="<?php echo $user['user_image']?>" /></h1>
	<div>
		<form name="" action="<?php echo base_url('actions/feedback/add_user_data_feedback'); ?>" method="post">
			<fieldset>
				<legend><?php echo $action_data['data']['feedback_question_message']; ?></legend>
				<div class="control-group">
					<textarea name="user_feedback" class="span4"></textarea>
				</div>
				<div class="control-group">
					<label class="control-label" for="user_score">
						<?php echo $action_data['data']['feedback_vote_message']; ?>
					</label>
					<div class="controls">
						<select name="user_score" class="span1">
							<<option value="5">5</option>
							<<option value="4">4</option>
							<<option value="3">3</option>
							<<option value="2">2</option>
							<<option value="1">1</option>
						</select>
					</div>
				</div>
				<input type="hidden" name="action_data_hash" value="<?php echo $action_data['hash']; ?>" />
				<div class="control-group">
					<button type="submit" class="btn">Submit</button>
				</div>
			</fieldset>
		</form>
	</div>
</div>

