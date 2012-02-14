<div id="component-sharebutton-form" class="app-component-form">
<?php if($this->input->get('sharebutton_settings_success')){ ?>
        <div class="notice success">success</div><?php
} else if ($this->input->get('error')){ ?>
        <div class="notice success">error</div><?php
}
$attributes = array('class' => 'component-sharebutton-form', 'id' => '');
echo form_open_multipart('settings/app_component/sharebutton/'.$app_install_id.'/'.$campaign_id, $attributes); ?>
<ul class="on-off">
<li>
	<label for="share_on_facebook">Facebook Share : </label>
        <input type="checkbox" id="share_on_facebook" name="share_on_facebook" value="1" class="" <?php echo set_checkbox('share_on_facebook', '1', $sharebutton['facebook_button']); ?>>          
</li> 
<li>
        <label for="share_on_twitter">Twitter Share : </label>
        <input type="checkbox" id="share_on_twitter" name="share_on_twitter" value="1" class="" <?php echo set_checkbox('share_on_twitter', '1', $sharebutton['twitter_button']); ?>>
</li> 
</ul>

<h3>Share message</h3>
<ul>
<li <?php echo form_error('share_image') ? 'class="error"' : ''; ?>>
        <label for="share_image">Picture : <span class="required">*</span></label>
        <div class="upload-pic">
                <?php if($sharebutton['message']['image']) { ?>
                <p class="pic">
                        <img src="<?php echo imgsize($sharebutton['message']['image'], 'large'); ?>" />
                </p>
                <?php } ?>
                <p class="browse">
                            <input id="share_image" type="file" name="share_image" style="opacity:0;filter: Alpha(Opacity=0);height:29px;position: absolute;width: 114px; ">
                            <a class="bt-browse_pic" href="#"><span>Browse picture</span></a>
                </p>
        </div>
</li>

<li <?php echo form_error('share_title') ? 'class="error"' : ''; ?>>
        <label for="share_title">Title : <span class="required">*</span></label>
        <div class="inputs">
                <input id="share_title" style="width:250px;" type="text" name="share_title"  value="<?php echo set_value('share_title',$sharebutton['message']['title']); ?>"  />
        </div>
</li>

<li <?php echo form_error('share_caption') ? 'class="error"' : ''; ?>>
        <label for="share_caption">Caption : <span class="required">*</span></label>
        <div class="inputs">
                <input id="share_caption" style="width:250px;" type="text" name="share_caption"  value="<?php echo set_value('share_caption',$sharebutton['message']['caption']); ?>"  />
        </div>
</li>

<li <?php echo form_error('share_description') ? 'class="error"' : ''; ?>>
        <label for="share_description">Message : <span class="required">*</span></label>
        <div class="inputs">						
	<?php echo form_textarea( array( 'name' => 'share_description', 'style' => 'width:254px;height:100px;', 'value' => set_value('share_description',$sharebutton['message']['text']) ) )?>
        </div>
</li>
</ul>

<h3>Scoring Criteria</h3>
<ul class="cols2">
<li <?php echo form_error('share_score') ? 'class="error"' : ''; ?>>
        <label for="share_score">Share : <span class="required">*</span></label>
        <input id="share_score" type="text" name="share_score"  value="<?php echo set_value('share_score',$sharebutton['criteria']['score']); ?>"  />
        Points
</li>

<li <?php echo form_error('share_cooldown') ? 'class="error"' : ''; ?>>
        <label for="share_cooldown">Share Cooldown : <span class="required">*</span></label>
        <input id="share_cooldown" type="text" name="share_cooldown"  value="<?php echo set_value('share_cooldown',$sharebutton['criteria']['cooldown']); ?>"  />
        Hour(s)
</li>

<li <?php echo form_error('share_maximum') ? 'class="error"' : ''; ?>>
        <label for="share_maximum">Max : <span class="required">*</span></label>
        <input id="share_maximum" type="text" name="share_maximum"  value="<?php echo set_value('share_maximum',$sharebutton['criteria']['maximum']); ?>"  />
        Times
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