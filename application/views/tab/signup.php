<div class="popup-fb signup">
    
	<h2>Register</h2>
	
	<div class="step">
	<span class="connector"></span>
	<ul>
		<li class="active"><span>1</span></li>
		<li><span>2</span></li>
		<li><span>3</span></li>
	</ul>
	</div>
	
    <div><img src="<?php echo base_url(); ?>images/register-step-01.jpg" /></div>

	<div id="signup-form">
		<?php $attributes = array('class' => 'signup-form', 'id' => ''); echo form_open_multipart('tab/signup_submit/'.$page_id, $attributes); ?>
        <div class="profile">
			<img src="<?php echo $user_profile_picture;?>" />
			<div class="upload-pic">
				<span><a href="#">(Change)</a></span>
				<input id="user_image" type="file" name="user_image">
			</div>
			<p class="name"><?php echo $facebook_user['first_name'].' '.$facebook_user['last_name'];?></p>
        </div>
        <div class="form">
			<h2>Account information</h2>
			<ul>
				<li data-field-name="first_name">
					<label class="title">
						<span class="field-label"><span class="required"> * </span>Firstname : </span>
					</label>
					<input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name'); ?>" placeholder="Enter real firstname" />
				</li>
				<li data-field-name="last_name">
					<label class="title">
						<span class="field-label"><span class="required"> * </span>Lastname : </span>
					</label><input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name'); ?>" placeholder="Enter real lastname" /></li>
				<li data-field-name="email">
					<label class="title">
						<span class="field-label"><span class="required"> * </span>Email : </span>
					</label>
					<input id="email" type="text" name="email" maxlength="255" value="<?php echo set_value('email'); ?>" placeholder="someone@example.com" />
					<?php echo form_error('email')? '<p>Enter email in the format : someone@example.com.</p>':''; ?>
				</li>
			</ul>
			<div class="buttons">
				 <p class="right">
					<a class="bt-cancel"><span>Cancel</span></a>
					<a class="bt-next-inactive"><span>Next</span></a>
					<?php echo form_submit( 'submit-form', 'Submit', 'style="display:none"'); ?>
				</p>
			</div>
        </div>
		<?php echo form_close(); ?>	
    </div>

</div>