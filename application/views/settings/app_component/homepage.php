<div id="component-homepage-form">
<?php // Change the css classes to suit your needs    

$attributes = array('class' => 'component-homepage-form', 'id' => '');
echo form_open('settings/app_component/homepage/'.$app_install_id.'/'.$campaign_id, $attributes); ?>

<p>
        <?php echo form_error('homepage_for_non_fans'); ?>
        
        <?php // Change the values/css classes to suit your needs ?>
        <br /><input type="checkbox" id="homepage_for_non_fans" name="homepage_for_non_fans" value="1" class="" <?php echo set_checkbox('homepage_for_non_fans', '1', $homepage['enable']); ?>> 
                   
	<label for="homepage_for_non_fans">Homepage for non-fans</label>
</p> 
<p>
        <label for="graphic">Graphic <span class="required">*</span></label>
        <?php echo form_error('graphic'); ?>
        <br /><input id="graphic" type="text" name="graphic" maxlength="255" value="<?php echo set_value('graphic', $homepage['image']); ?>"  />
</p>

<p>
        <label for="message">Message <span class="required">*</span></label>
	<?php echo form_error('message'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => 'message', 'rows' => '5', 'cols' => '80', 'value' => set_value('message', $homepage['message']) ) )?>
</p>

<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
<a class="a-back-to-campaign-list" href="<?php echo base_url().'settings/campaign/'. $app_install_id;?>">Back</a>
</div>