<div id="component-homepage-form" class="app-component-form">
<?php if($this->input->get('homepage_settings_success')){ ?>
        <div class="notice success">success</div><?php
} else if ($this->input->get('error')){ ?>
        <div class="notice success">error</div><?php
}
$attributes = array('class' => 'component-homepage-form', 'id' => '');
echo form_open_multipart('settings/app_component/homepage/'.$app_install_id, $attributes); ?>
<ul>
<li>
        <label for="homepage_for_non_fans">Homepage for non-fans :</label>
        <div class="inputs">
                <input type="checkbox" id="homepage_for_non_fans" name="homepage_for_non_fans" value="1" class="" <?php echo set_checkbox('homepage_for_non_fans', '1', issetor($homepage['enable'])); ?>> 
        </div>
</li> 
<li <?php echo form_error('graphic') ? 'class="error"' : ''; ?>>
        <label for="graphic">Graphic : <span class="required">*</span></label>
        <div class="upload-pic">
                <?php if(issetor($homepage['image'])) { ?>
                <p class="pic">
                        <img src="<?php echo imgsize($homepage['image'], 'large'); ?>" />
                </p>
                <?php } ?>
                <p class="browse">
                            <input id="graphic" type="file" name="graphic" style="opacity:0;filter: Alpha(Opacity=0);height:29px;position: absolute;width: 114px; ">
                            <a class="bt-browse_pic" href="#"><span>Browse picture</span></a>
                </p>
        </div>
</li>

<li <?php echo form_error('message') ? 'class="error"' : ''; ?>>
        <label for="message">Message : <span class="required">*</span></label>
	<div class="inputs">						
        	<?php echo form_textarea( array( 'name' => 'message', 'style' => 'width:288px;height:100px;', 'value' => set_value('message', issetor($homepage['message'])) ) )?>
        </div>
</li>

<li>
        <div class="buttons">
                <a class="a-back-to-app-settings bt-back" data-app-install-id="<?php echo $app_install_id;?>" href="<?php echo base_url().'settings/campaign/'. $app_install_id;?>">Back</a>
                <?php echo form_submit( array('submit'=>'Submit', 'class'=>'bt-update')); ?>
        </div>
</li>
</ul>
<?php echo form_close(); ?>
</div>