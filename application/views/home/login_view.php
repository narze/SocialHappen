<div class="popup_login">
  <h2 class="in-popup" style="width:400px;">Login</h2>
  <?php

  $attributes = array('class' => '', 'id' => 'merchant_login_form');
  echo form_open('home/login'.$next, $attributes); ?>
  <?php if(isset($login_failed)) : ?>
    <div class="notice error">Wrong email, phone or password.</div>
  <?php endif; ?>
  <?php if(isset($email_and_phone_not_entered)) : ?>
    <p class="login-fail">Email and mobile phone number not entered</p>
  <?php endif; ?>
  <p>
    <label for="email">Email <span class="required">*</span></label>
    <?php echo form_error('email'); ?>
    <br /><input id="email" type="text" name="email" maxlength="100" value="<?php echo set_value('email'); ?>"  />
  </p>

  <p>
    <label for="mobile_phone_number">Mobile Phone Number <span class="required">*</span></label>
    <?php echo form_error('mobile_phone_number'); ?>
    <br /><input id="mobile_phone_number" type="text" name="mobile_phone_number" maxlength="20" value="<?php echo set_value('mobile_phone_number'); ?>"  />
  </p>

  <p>
    <label for="password">Password <span class="required">*</span></label>
    <?php echo form_error('password'); ?>
    <br /><input id="password" type="password" name="password" maxlength="50" value="<?php echo set_value('password'); ?>"  />
  </p>


  <p>
    <?php echo form_submit( 'submit', 'Submit', 'class="bt-continue"'); ?>
  </p>

  <?php echo form_close(); ?>
</div>