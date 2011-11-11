<div id="component-invite-form">
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'component-invite-form', 'id' => '');
echo form_open('settings/app_component/invite/'.$app_install_id.'/'.$campaign_id, $attributes); ?>

<p>
	
        <?php echo form_error('facebook'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="facebook" name="facebook" value="1" class="" <?php echo set_checkbox('facebook', '1', $invite['facebook_invite']); ?>> 
                   
	<label for="facebook">Facebook</label>
</p> 
<p>
	
        <?php echo form_error('email'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="email" name="email" value="1" class="" <?php echo set_checkbox('email', '1', $invite['email_invite']); ?>> 
                   
	<label for="email">Email</label>
</p> 
<p>
        <label for="invite_score">Invite score <span class="required">*</span></label>
        <?php echo form_error('invite_score'); ?>
        <br /><input id="invite_score" type="text" name="invite_score"  value="<?php echo set_value('invite_score',$invite['criteria']['score']); ?>"  />
</p>

<p>
        <label for="maximum_invite">Maximum invite <span class="required">*</span></label>
        <?php echo form_error('maximum_invite'); ?>
        <br /><input id="maximum_invite" type="text" name="maximum_invite"  value="<?php echo set_value('maximum_invite',$invite['criteria']['maximum']); ?>"  />
</p>

<p>
        <label for="invite_cooldown">Invite cooldown <span class="required">*</span></label>
        <?php echo form_error('invite_cooldown'); ?>
        <br /><input id="invite_cooldown" type="text" name="invite_cooldown"  value="<?php echo set_value('invite_cooldown',$invite['criteria']['cooldown']); ?>"  />
</p>

<p>
        <label for="page_acceptance_score">Page acceptance score <span class="required">*</span></label>
        <?php echo form_error('page_acceptance_score'); ?>
        <br /><input id="page_acceptance_score" type="text" name="page_acceptance_score"  value="<?php echo set_value('page_acceptance_score',$invite['criteria']['acceptance_score']['page']); ?>"  />
</p>

<p>
        <label for="app_acceptance_score">App acceptance score <span class="required">*</span></label>
        <?php echo form_error('app_acceptance_score'); ?>
        <br /><input id="app_acceptance_score" type="text" name="app_acceptance_score"  value="<?php echo set_value('app_acceptance_score',$invite['criteria']['acceptance_score']['campaign']); ?>"  />
</p>

<p>
        <label for="invite_image">Invite image <span class="required">*</span></label>
        <?php echo form_error('invite_image'); ?>
        <br /><input id="invite_image" type="text" name="invite_image" maxlength="255" value="<?php echo set_value('invite_image',$invite['message']['image']); ?>"  />
</p>

<p>
        <label for="invite_title">Invite title <span class="required">*</span></label>
        <?php echo form_error('invite_title'); ?>
        <br /><input id="invite_title" type="text" name="invite_title" maxlength="255" value="<?php echo set_value('invite_title',$invite['message']['title']); ?>"  />
</p>

<p>
        <label for="invite_text">Invite text <span class="required">*</span></label>
        <?php echo form_error('invite_text'); ?>
        <br /><input id="invite_text" type="text" name="invite_text"  value="<?php echo set_value('invite_text',$invite['message']['text']); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
<a class="a-back-to-campaign-list" href="<?php echo base_url().'settings/campaign/'. $app_install_id;?>">Back</a>
</div>