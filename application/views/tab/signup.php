<div id="signup-form">
<?php // Change the css classes to suit your needs    
	$attributes = array('class' => 'signup-form', 'id' => '');
	echo form_open('tab/signup', $attributes); ?>
	<div id="user-information"><h2>User information</h2>
		<p>
				<label for="first_name">First name <span class="required">*</span></label>
				<?php echo form_error('first_name'); ?>
				<br /><input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name'); ?>"  />
		</p>
		
		<p>
				<label for="last_name">Last name <span class="required">*</span></label>
				<?php echo form_error('last_name'); ?>
				<br /><input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name'); ?>"  />
		</p>
		
		<p>
				<label for="email">Email <span class="required">*</span></label>
				<?php echo form_error('email'); ?>
				<br /><input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>"  />
		</p>
		
		<p>
				<label for="user_image">User image</label>
				<?php echo form_error('user_image'); ?>
				<br /><input id="user_image" type="file" name="user_image" />
				<img src="<?php echo $user_profile_picture;?>" />
		</p>
	</div>
	<p>
			<?php echo form_submit( 'submit-form', 'Submit'); ?>
	</p>
	<?php echo form_close(); ?>
</div>