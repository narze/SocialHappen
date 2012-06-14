<div class="container-fluid">
  <div class="row-fluid">
    <div class="span4">&nbsp;</div>
      <div class="span">
        <?php if(isset($success)) : ?>
        <div class="well" id="updated-notice">
          <span>Updated</span>
        </div>
        <?php endif; ?>
        <div class="well" id="authorize-information">
        
        <legend>Authorize information</legend>
        <div>
          
          <p><strong>Facebook ID: </strong><a href="<?php echo $user_facebook['link'];?>"><?php echo $user_facebook['name'];?></a></p>
          <p><strong>Joined since : </strong><?php echo $user['user_register_date'];?></p>
          <p><strong>Last Active : </strong><?php echo $user['user_last_seen'];?></p>
         
        </div>
      </div>

      <div class="well" id="account-information">
        <legend>Account information</legend>

        <?php // Change the css classes to suit your needs
          $attributes = array('class' => 'form-horizontal account-information', 'id' => '');
          echo form_open_multipart("player/settings", $attributes); ?>
          <div>
            <div class="control-group">
              <label class="control-label">Picture profile :</label>
              <div class="controls">
                <?php echo form_error('user_image'); ?>
                <div class="help-block">

                    <img class="user-image" src="<?php echo imgsize($user['user_image'],'square');?>" />
                    <span class="help-inline">
                      <span>
                        <input id="user_image" type="file" name="user_image" style="opacity:0;filter: Alpha(Opacity=0);height:18px;position: absolute;width: 144px; "/>
                        <a class="bt-change_pic" href="#">
                          <span>Change picture</span>
                        </a>
                      </span>
                      <label class="checkbox" for="use_facebook_picture">
                        <span>
                          <input type="checkbox" id="use_facebook_picture" name="use_facebook_picture" <?php echo set_checkbox('use_facebook_picture', NULL, FALSE); ?>>
                        Use your facebook avatar
                        </span>
                        </label>
                    </span>
                </div>
              </div>
            </div>

            <div class="control-group">
              <label class="control-label">First name :</label>
              <div class="controls">
                <?php echo form_error('first_name'); ?><input id="first_name" type="text" name="first_name" maxlength="255" value="<?php echo set_value('first_name',$user['user_first_name']); ?>"  />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Last name :</label>
              <div class="controls">
                <?php echo form_error('last_name'); ?><input id="last_name" type="text" name="last_name" maxlength="255" value="<?php echo set_value('last_name',$user['user_last_name']); ?>"  />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Email :</label>
              <div class="controls">
                <input type="text" disabled="true" value="<?php echo set_value('email',$user['user_email']); ?>"  />
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">About me :</label>
              <div class="controls">
                <?php echo form_error('about'); ?><?php echo form_textarea( array( 'name' => 'about', 'id' => 'about' , 'cols'=> 30 ,'value' => html_entity_decode(set_value('about',$user['user_about'])) ) ); ?>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Timezone :</label>
              <div class="controls">
                <?php echo timezone_menu($user['user_timezone']);?>
              </div>
            </div>
            <div class="control-group text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
                    
          </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>