<?php // Change the css classes to suit your needs    

$attributes = array('class' => '', 'id' => '');
echo form_open('player/signup', $attributes); ?>

<p>
        <?php if(isset($duplicated_email)) { echo '<div>This email is already a SocialHappen user.</div>'; } ?>
        <label for="email">Email <span class="required">*</span></label>
        <?php echo form_error('email'); ?>
        <br /><input id="email" type="text" name="email" maxlength="100" value="<?php echo set_value('email'); ?>"  />
</p>

<p>
        <?php if(isset($duplicated_phone)) { echo '<div>This phone number is already a SocialHappen user.</div>'; } ?>
        <label for="mobile_phone_number">Mobile Phone Number <span class="required">*</span></label>
        <?php echo form_error('mobile_phone_number'); ?>
        <br /><input id="mobile_phone_number" type="text" name="mobile_phone_number" maxlength="20" value="<?php echo set_value('mobile_phone_number'); ?>"  />
</p>

<p>     
        <?php if(isset($password_not_match)) { echo '<div>Password Not Match</div>'; } ?>
        <label for="password">Password <span class="required">*</span></label>
        <?php echo form_error('password'); ?>
        <br /><input id="password" type="password" name="password" maxlength="50" value="<?php echo set_value('password'); ?>"  />
</p>

<p>
        <label for="password_again">Password Again <span class="required">*</span></label>
        <?php echo form_error('password_again'); ?>
        <br /><input id="password_again" type="password" name="password_again" maxlength="50" value="<?php echo set_value('password_again'); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
