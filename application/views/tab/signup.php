<div class="popup-fb-2col">
    <div>
      <div><img src="<?php echo base_url(); ?>images/slide-show260-190.jpg" alt="" /></div>
      <div id="signup-form">

<?php // Change the css classes to suit your needs    
		$attributes = array('class' => 'signup-form', 'id' => '');
		echo form_open('tab/signup', $attributes); ?>
        <div class="profile">
          <p><?php echo $facebook_user['first_name'];?><span><?php echo $facebook_user['last_name'];?></span></p>
          <p class="thumb">
				<img src="<?php echo $user_profile_picture;?>" />
		  </p>
		  <div class="upload-pic">
					<span><a href="#">(Change)</a></span>
					<input id="user_image" type="file" name="user_image">
			</div>
        </div>
		<br class="clear" />
        <div class="form">
          <h2>Account information</h2>
          <ul>
            <li><label <?php echo form_error('first_name')? 'class="error" ':''; ?>>Firstname: </label><input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name'); ?>"  /></li>
            <li><label <?php echo form_error('last_name')? 'class="error" ':''; ?>>Lastname: </label><input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name'); ?>"  /></li>
            <li><label <?php echo form_error('email')? 'class="error" ':''; ?>>Email: </label><input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>"  /></li>
          </ul>
          <p>
				<a class="bt-register-now" href="#"><span>Register now</span></a>
				<?php echo form_submit( 'submit-form', 'Submit', 'style="display:none"'); ?>
		  </p>
        </div>
<?php echo form_close(); ?>

		
      </div>
    </div>
</div>