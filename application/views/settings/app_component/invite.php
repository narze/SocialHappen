<div id="component-invite-form" class="app-component-form">
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'component-invite-form', 'id' => '');
echo form_open('settings/app_component_invite/'.$app_install_id.'/'.$campaign_id, $attributes); ?>

<ul class="on-off">
<li>
	<label for="facebook">Facebook Invite :</label>
        <input type="checkbox" id="facebook" name="facebook" value="1" class="" <?php echo set_checkbox('facebook', '1', $invite['facebook_invite']); ?>> 
</li>
<li>
        <label for="email">Email Invite :</label>
        <input type="checkbox" id="email" name="email" value="1" class="" <?php echo set_checkbox('email', '1', $invite['email_invite']); ?>>          
</li>	
</ul> 

<h3>Invititation message</h3>
<ul>
<li <?php echo form_error('invite_image') ? 'class="error"' : ''; ?>>
        <label for="invite_image">Picture : <span class="required">*</span></label>
        <div class="upload-pic">
                <?php if($invite['message']['image']) { ?>
                <p class="pic">
                        <img src="<?php echo $invite['message']['image']; ?>" width="64" height="64" />
                </p>
                <?php } ?>
                <p class="browse">
                            <input id="invite_image" type="file" name="invite_image" style="opacity:0;filter: Alpha(Opacity=0);height:29px;position: absolute;width: 114px; ">
                            <a class="bt-browse_pic" href="#"><span>Browse picture</span></a>
                </p>
        </div>
</li>

<li <?php echo form_error('invite_title') ? 'class="error"' : ''; ?>>
        <label for="invite_title">Title : <span class="required">*</span></label>
        <div class="inputs">
                <input style="width:300px;" id="invite_title" type="text" name="invite_title" maxlength="255" value="<?php echo set_value('invite_title',$invite['message']['title']); ?>"  />
        </div>
</li>

<li <?php echo form_error('invite_text') ? 'class="error"' : ''; ?>>
        <label for="invite_text">Message : <span class="required">*</span></label>
        <div class="inputs">
                <?php echo form_textarea( array( 'name' => 'invite_text', 'id' => 'invite_text', 'style' => 'width:304px;height:100px;', 'value' => set_value('invite_text',$invite['message']['text']) ) )?>
        </div>
</li>
</ul>

<h3>Scoring Criteria</h3>
<ul class="cols2">
<li <?php echo form_error('invite_score') ? 'class="error"' : ''; ?>>
        <label for="invite_score">Invite : <span class="required">*</span></label>
        <input id="invite_score" type="text" name="invite_score"  value="<?php echo set_value('invite_score',$invite['criteria']['score']); ?>"  />
        Points
</li>

<li <?php echo form_error('invite_cooldown') ? 'class="error"' : ''; ?>>
        <label for="invite_cooldown">Invite cooldown : <span class="required">*</span></label>
        <input id="invite_cooldown" type="text" name="invite_cooldown"  value="<?php echo set_value('invite_cooldown',$invite['criteria']['cooldown']); ?>"  />
        Hour(s)
</li <?php echo form_error('invite_score') ? 'class="error"' : ''; ?>>

<li <?php echo form_error('maximum_invite') ? 'class="error"' : ''; ?>>
        <label for="maximum_invite">Max : <span class="required">*</span></label>
        <input id="maximum_invite" type="text" name="maximum_invite"  value="<?php echo set_value('maximum_invite',$invite['criteria']['maximum']); ?>"  />
        Times
</li>

<li <?php echo form_error('page_acceptance_score') ? 'class="error"' : ''; ?>>
        <label for="page_acceptance_score">Page acceptance : <span class="required">*</span></label>
        <input id="page_acceptance_score" type="text" name="page_acceptance_score"  value="<?php echo set_value('page_acceptance_score',$invite['criteria']['acceptance_score']['page']); ?>"  />
        Points
</li>

<li <?php echo form_error('app_acceptance_score') ? 'class="error"' : ''; ?>>
        <label for="app_acceptance_score">App acceptance : <span class="required">*</span></label>
        <input id="app_acceptance_score" type="text" name="app_acceptance_score"  value="<?php echo set_value('app_acceptance_score',$invite['criteria']['acceptance_score']['campaign']); ?>"  />
        Points
</li>
</ul>

<ul>
<li>
        <div class="buttons">
                <a class="a-back-to-campaign-list bt-back" href="<?php echo base_url().'settings/campaign/'. $app_install_id;?>">Back</a>
                <?php echo form_submit( array('submit'=>'Submit', 'class'=>'bt-update')); ?>
        </div>
</li>
</ul>

<?php echo form_close(); ?>
</div>