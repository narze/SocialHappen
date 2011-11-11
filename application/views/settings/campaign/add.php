<div id="new-campaign-form">
<?php 
$attributes = array('class' => 'new-campaign-form', 'id' => '');
echo form_open('settings/campaign/add/'.$app_install_id, $attributes); ?>

<p>
        <label for="campaign_name">Campaign Name <span class="required">*</span></label>
        <?php echo form_error('campaign_name'); ?>
        <br /><input id="campaign_name" type="text" name="campaign_name" maxlength="255" value="<?php echo set_value('campaign_name'); ?>"  />
</p>

<p>
        <label for="campaign_start_date">Campaign Start Date <span class="required">*</span></label>
        <?php echo form_error('campaign_start_date'); ?>
        <br /><input id="campaign_start_date" type="text" name="campaign_start_date" maxlength="10" value="<?php echo set_value('campaign_start_date'); ?>"  />
</p>

<p>
        <label for="campaign_end_date">Campaign End Date <span class="required">*</span></label>
        <?php echo form_error('campaign_end_date'); ?>
        <br /><input id="campaign_end_date" type="text" name="campaign_end_date" maxlength="10" value="<?php echo set_value('campaign_end_date'); ?>"  />
</p>

<p>
        <label for="campaign_end_message">Campaign End Message</label>
	<?php echo form_error('campaign_end_message'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => 'campaign_end_message', 'rows' => '5', 'cols' => '80', 'value' => set_value('campaign_end_message') ) )?>
</p>

<p>
        <?php echo form_submit( 'submit', 'Submit'); ?>
</p>

<?php echo form_close(); ?>
<a class="a-back-to-campaign-list" href="<?php echo base_url().'settings/campaign/'. $app_install_id;?>">Back</a>
</div>