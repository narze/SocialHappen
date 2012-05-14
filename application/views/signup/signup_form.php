<div class="container-fluid">
  <div class="row-fluid">
    <div class="span4">&nbsp;</div>

    <?php $attributes = array('class' => 'span4 well', 'id' => 'signup-form');
    echo form_open(base_url('signup/form'.$next), $attributes); ?>

      <?php if(isset($duplicated_email)) : ?>
        <div class="alert alert-error">
          <a class="close" data-dismiss="alert">×</a>
          This email is already a SocialHappen user.
        </div>
      <?php endif; ?>

      <?php if(isset($duplicated_phone)) : ?>
        <!-- <div class="alert alert-error">
          <a class="close" data-dismiss="alert">×</a>
          This phone number is already a SocialHappen user.
        </div> -->
      <?php endif; ?>

      <div class="control-group <?php echo form_error('email') ? 'error': ''; ?>">
        <label for="email">Email</label>
        <input id="email" class="span4" type="text" name="email" maxlength="100" value="<?php echo set_value('email'); ?>"  />
        <?php echo form_error('email'); ?>
      </div>

      <div class="control-group <?php echo form_error('first_name') ? 'error': ''; ?>">
        <label for="first_name">Firstname</label>
        <input id="first_name" class="span4" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name'); ?>"  />
        <?php echo form_error('first_name'); ?>
      </div>

      <div class="control-group <?php echo form_error('last_name') ? 'error': ''; ?>">
        <label for="last_name">Lastname</label>
        <input id="last_name" class="span4" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name'); ?>"  />
        <?php echo form_error('last_name'); ?>
      </div>

      <!-- <div class="control-group <?php echo form_error('mobile_phone_number') ? 'error': ''; ?>">
        <label for="mobile_phone_number">Mobile Phone Number</label>
        <input id="mobile_phone_number" class="span4" type="text" name="mobile_phone_number" maxlength="20" value="<?php echo set_value('mobile_phone_number'); ?>"  />
        <?php echo form_error('mobile_phone_number'); ?>
      </div> -->

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

      <input type="hidden" id="timezone" name="timezone" value="UTC" />
      
      <div>
        <button type="submit" class="btn btn-primary">Signup</button>
      </div>
    <?php echo form_close(); ?>
</div>
