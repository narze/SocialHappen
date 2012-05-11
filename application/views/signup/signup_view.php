<div class="container-fluid">
  <div class="row-fluid">
    <div class="span4">&nbsp;</div>

    <?php $attributes = array('class' => 'span4 well', 'id' => '');
    echo form_open('signup'.$next ? "?next={$next}" : '', $attributes); ?>

      <?php if(isset($duplicated_email)) : ?>
        <div class="alert alert-error">
          <a class="close" data-dismiss="alert">×</a>
          This email is already a SocialHappen user.
        </div>
      <?php endif; ?>

      <?php if(isset($duplicated_phone)) : ?>
        <div class="alert alert-error">
          <a class="close" data-dismiss="alert">×</a>
          This phone number is already a SocialHappen user.
        </div>
      <?php endif; ?>

      <?php if(isset($email_and_phone_not_entered)) : ?>
        <p class="login-fail">Email and mobile phone number not entered</p>
      <?php endif; ?>
      
      <?php if($facebook_user): ?>
        <div class="control-group">
          <span>Register as <?php echo $facebook_user['name'] ?> <img src="https://graph.facebook.com/<?php echo $facebook_user['id'] ?>/picture" /></span>
        </div> 
      <?php else : ?>
        <div class="control-group">
          <span><a onclick="fbLogin(fbLoginResult)">Signup with facebook</a></span>
        </div>   
      <?php endif; ?>

      <div class="control-group <?php echo form_error('email') ? 'error': ''; ?>">
        <label for="email">Email</label>
        <input id="email" class="span4" type="text" name="email" maxlength="100" value="<?php echo set_value('email'); ?>"  />
        <?php echo form_error('email'); ?>
      </div>

      <div class="control-group <?php echo form_error('mobile_phone_number') ? 'error': ''; ?>">
        <label for="mobile_phone_number">Mobile Phone Number</label>
        <input id="mobile_phone_number" class="span4" type="text" name="mobile_phone_number" maxlength="20" value="<?php echo set_value('mobile_phone_number'); ?>"  />
        <?php echo form_error('mobile_phone_number'); ?>
      </div>

      <div class="control-group <?php echo form_error('password') ? 'error': ''; ?>">
        <label for="password">Password</label>
        <input id="password" class="span4" type="password" name="password" maxlength="50" value="<?php echo set_value('password'); ?>"  />
        <?php echo form_error('password'); ?>
      </div>

      <div class="control-group <?php echo form_error('password_again') ? 'error': ''; ?>">
        <label for="password_again">Password Again</label>
        <input id="password_again" class="span4" type="password" name="password_again" maxlength="50" value="<?php echo set_value('password_again'); ?>"  />
        <?php echo form_error('password_again'); ?>
        <?php if(isset($password_not_match)) { echo '<span class="help-block">Password Not Match</span>'; } ?>
      </div>

      <div>
        <button type="submit" class="btn btn-primary">Signup</button>
      </div>

      

    <?php echo form_close(); ?>
</div>

<script type="text/template" id="facebook-login-template">
  <div class="control-group">
    <span><a onclick="fbLogin(fbLoginResult)">Signup with facebook</a></span>
  </div>        
</script>
