<div class="container-fluid">
  <div id="login">
    <div class="row-fluid text-center">
      <div class="span4">&nbsp;</div>
      <div class="span4 well">
        <span class="login-form-facebook-connect">
          <button id="login-form-facebook-connect"
            class="btn btn-info" data-toggle="button" 
            onclick="fbLogin(fbLoginResult)"
            data-redirect="<?php echo 'signup/facebook'.$next;?>">
              Connect with facebook
          </button>
        </span>
      </div>
    </div>
    <div class="row-fluid"><center class="center-or">or<center></div>
    <div class="row-fluid">
      <div class="span4">&nbsp;</div>

      <?php $attributes = array('class' => 'span4 well', 'id' => '');
      echo form_open('login'.$next, $attributes); ?>
        <?php if(isset($login_failed)) : ?>
          <div class="alert alert-error">
            <a class="close" data-dismiss="alert">×</a>
            Login Failure
          </div>
        <?php endif; ?>

        <?php if(isset($email_and_phone_not_entered)) : ?>
          <!-- <p class="login-fail">Email and mobile phone number not entered</p> -->
        <?php endif; ?>
        
        <div class="control-group <?php echo form_error('email') ? 'error': ''; ?>">
          <label for="email">Email</label>
          <input id="email" class="span12" type="text" name="email" maxlength="100" value="<?php echo set_value('email'); ?>"  />
          <?php echo form_error('email'); ?>
        </div>

        <!--<div class="control-group <?php echo form_error('mobile_phone_number') ? 'error': ''; ?>">
          <label for="mobile_phone_number">Mobile Phone Number</label>
          <input id="mobile_phone_number" class="span4" type="text" name="mobile_phone_number" maxlength="20" value="<?php echo set_value('mobile_phone_number'); ?>"  />
          <?php echo form_error('mobile_phone_number'); ?>
        </div> -->

        <div class="control-group <?php echo form_error('password') ? 'error': ''; ?>">
          <label for="password">Password</label>
          <input id="password" class="span12" type="password" name="password" maxlength="50" value="<?php echo set_value('password'); ?>"  />
          <?php echo form_error('password'); ?>
        </div>

        <div class="control-group">
          <button type="submit" class="btn btn-primary">Login</button><span> or </span><a href="<?php echo base_url('signup');?>" class="btn">Signup SocialHappen</a>
        </div>
      <?php echo form_close(); ?>
    </div>
  </div>
</div>

<script type="text/javascript">
  //When FB init, show signup buttons
  checkFBConnected(function(){
    $('.facebook-connect').show();
  });

  //redirect when connected facebook
  window.fbLoginResult = function(connected) {
    console.log(connected);
    if(connected) {
      window.location = base_url + 'login/success?redirect=' + $('#login-form-facebook-connect').data('redirect');
    }
  };
</script>

