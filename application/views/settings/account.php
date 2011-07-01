<div class="form">
	<?php if(isset($success)) echo 'Updated'; ?>
		<div id="authorize-information"><h2>Authorize information</h2></div>
<?php // Change the css classes to suit your needs    
	$attributes = array('class' => 'account', 'id' => '');
	echo form_open_multipart("settings/account/{$user['user_id']}", $attributes); ?>
	<div id="account-information"><h2>Account information</h2>
		
		<p>
				<label for="first_name">First name <span class="required">*</span></label>
				<?php echo form_error('first_name'); ?>
				<br /><input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name',$user['user_first_name']); ?>"  />
		</p>
		
		<p>
				<label for="last_name">Last name <span class="required">*</span></label>
				<?php echo form_error('last_name'); ?>
				<br /><input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name',$user['user_last_name']); ?>"  />
		</p>
		
		<p>
				<label for="email">Email <span class="required">*</span></label>
				<?php echo form_error('email'); ?>
				<br /><input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email',$user['user_email']); ?>"  />
		</p>
			
		<p>
				<label for="user_image">User image</label>
				<?php echo form_error('user_image'); ?>
				<br /><input id="user_image" type="file" name="user_image" />
				<img src="<?php echo imgsize($user['user_image'],50);?>" />
		</p>

	</div>
	
	<div id="sharing-information"><h2>Sharing informarion</h2></div>
	<div id="email-notification"><h2>Email notification</h2></div>
	<div id="close-account"><h2>Close account</h2></div>

	
	<p>
			<?php echo form_submit('submitForm', 'Submit'); ?>
	</p>
	
	<?php echo form_close(); ?>
</div>