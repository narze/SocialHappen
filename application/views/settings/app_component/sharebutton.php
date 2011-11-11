<div id="component-sharebutton-form">
<?php

$attributes = array('class' => 'component-sharebutton-form', 'id' => '');
echo form_open('settings/app_component/sharebutton/'.$app_install_id.'/'.$campaign_id, $attributes); ?>

<p>
	
        <?php echo form_error('share_on_facebook'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="share_on_facebook" name="share_on_facebook" value="1" class="" <?php echo set_checkbox('share_on_facebook', '1', $sharebutton['facebook_button']); ?>> 
                   
	<label for="share_on_facebook">Share on Facebook</label>
</p> 
<p>
	
        <?php echo form_error('share_on_twitter'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="share_on_twitter" name="share_on_twitter" value="1" class="" <?php echo set_checkbox('share_on_twitter', '1', $sharebutton['twitter_button']); ?>> 
                   
	<label for="share_on_twitter">Share on Twitter</label>
</p> 
<p>
        <label for="share_title">Share Title <span class="required">*</span></label>
        <?php echo form_error('share_title'); ?>
        <br /><input id="share_title" type="text" name="share_title"  value="<?php echo set_value('share_title',$sharebutton['message']['title']); ?>"  />
</p>

<p>
        <label for="share_caption">Share Caption <span class="required">*</span></label>
        <?php echo form_error('share_caption'); ?>
        <br /><input id="share_caption" type="text" name="share_caption"  value="<?php echo set_value('share_caption',$sharebutton['message']['caption']); ?>"  />
</p>

<p>
        <label for="share_image">Share Image <span class="required">*</span></label>
        <?php echo form_error('share_image'); ?>
        <br /><input id="share_image" type="text" name="share_image"  value="<?php echo set_value('share_image',$sharebutton['message']['image']); ?>"  />
</p>

<p>
        <label for="share_description">Share Description</label>
	<?php echo form_error('share_description'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => 'share_description', 'rows' => '5', 'cols' => '80', 'value' => set_value('share_description',$sharebutton['message']['text']) ) )?>
</p>
<p>
        <label for="share_score">Share Score <span class="required">*</span></label>
        <?php echo form_error('share_score'); ?>
        <br /><input id="share_score" type="text" name="share_score"  value="<?php echo set_value('share_score',$sharebutton['criteria']['score']); ?>"  />
</p>

<p>
        <label for="share_maximum">Share Maximum <span class="required">*</span></label>
        <?php echo form_error('share_maximum'); ?>
        <br /><input id="share_maximum" type="text" name="share_maximum"  value="<?php echo set_value('share_maximum',$sharebutton['criteria']['maximum']); ?>"  />
</p>

<p>
        <label for="share_cooldown">Share Cooldown <span class="required">*</span></label>
        <?php echo form_error('share_cooldown'); ?>
        <br /><input id="share_cooldown" type="text" name="share_cooldown"  value="<?php echo set_value('share_cooldown',$sharebutton['criteria']['cooldown']); ?>"  />
</p>


<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
<a class="a-back-to-campaign-list" href="<?php echo base_url().'settings/campaign/'. $app_install_id;?>">Back</a>
</div>