<div id="timezone">
      <?php echo form_open("settings/page_timezone/view/{$page_id}", array('class' => 'timezone')); 
      if($updated) echo '<p>Updated</p>'; ?>
      <?php echo form_error('timezone_list'); ?>
      <label for="timezone_list">Timezone <span class="required">*</span></label>
	  <?php 
      echo form_dropdown('timezone_list', $timezones, $timezone);
      echo form_submit('submitForm', 'Submit', 'class="bt-update"');
      echo form_close(); ?>
</div>