<div id="new-campaign-form" class="app-component-form">
<?php 
if (issetor($date_range_validation_error)){ ?>
        <div class="notice error">Date range is not valid.</div><?php
}
$attributes = array('class' => 'new-campaign-form', 'id' => '');
echo form_open('settings/campaign/add/'.$app_install_id, $attributes); ?>
<ul>
<li <?php echo form_error('campaign_name') ? 'class="error"' : ''; ?>>
        <label for="campaign_name"><span class="required">*</span>Campaign Name : </label>
        <div class="inputs">
                <input id="campaign_name" type="text" name="campaign_name" maxlength="255" value="<?php echo set_value('campaign_name'); ?>" style="width:300px;" />
        </div>
</li>

<li class="campaign-date <?php echo (form_error('campaign_start_date') || form_error('campaign_end_date')) ? ' error' : ''; ?>">
        <label for="campaign_start_date"><span class="required">*</span>Campaign Period : </label>
        <div class="inputs">
                <input <?php echo form_error('campaign_start_date') ? 'class="error"' : ''; ?> id="campaign_start_date" type="text" name="campaign_start_date" maxlength="10" style="width:140px;" value="<?php echo set_value('campaign_start_date'); ?>"  />
                <span class="str">Start Date</span> - 
                <input <?php echo form_error('campaign_end_date')? 'class="error"' : ''; ?> id="campaign_end_date" type="text" name="campaign_end_date" maxlength="10" style="width:140px;" value="<?php echo set_value('campaign_end_date'); ?>"  />
                <span class="end">End Date</span>
        </div>
</li>

<li>
        <label for="campaign_end_message">End Message : </label>
	<div class="inputs">							
	       <?php echo form_textarea( array( 'name' => 'campaign_end_message', 'style' => 'width:304px;height:100px;', 'value' => set_value('campaign_end_message') ) )?>
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