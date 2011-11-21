<div id="component-homepage-form" class="app-component-form">
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'component-homepage-form', 'id' => '');
echo form_open('settings/app_component_homepage/'.$app_install_id.'/'.$campaign_id, $attributes); ?>
<ul>
<li>
        <label for="homepage_for_non_fans">Homepage for non-fans :</label>
        <div class="inputs">
                <input type="checkbox" id="homepage_for_non_fans" name="homepage_for_non_fans" value="1" class="" <?php echo set_checkbox('homepage_for_non_fans', '1', $homepage['enable']); ?>> 
        </div>
</li> 
<li <?php echo form_error('graphic') ? 'class="error"' : ''; ?>>
        <label for="graphic">Graphic : <span class="required">*</span></label>
        <div class="inputs">
                <input id="graphic" type="text" name="graphic" maxlength="255" value="<?php echo set_value('graphic', $homepage['image']); ?>"  />
        </div>
</li>

<li <?php echo form_error('message') ? 'class="error"' : ''; ?>>
        <label for="message">Message : <span class="required">*</span></label>
	<div class="inputs">						
        	<?php echo form_textarea( array( 'name' => 'message', 'style' => 'width:300px;height:100px;', 'value' => set_value('message', $homepage['message']) ) )?>
        </div>
</li>

<li>
        <div class="buttons">
                <a class="a-back-to-campaign-list bt-back" href="<?php echo base_url().'settings/campaign/'. $app_install_id;?>">Back</a>
                <?php echo form_submit( array('submit'=>'Submit', 'class'=>'bt-update')); ?>
        </div>
</li>
</ul>
<?php echo form_close(); ?>
</div>