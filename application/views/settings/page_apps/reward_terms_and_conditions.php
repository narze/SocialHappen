<div id="reward-terms-and-conditions">
      <?php echo form_open("settings/page_reward/terms_and_conditions/{$page_id}", array('class' => '-terms-and-conditions')); 
      if($updated) echo '<p>Updated</p>'; ?>

  <p>
        <label for="terms_and_conditions">Terms and Conditions</label>
	<?php echo form_error('terms_and_conditions'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => 'terms_and_conditions', 'rows' => '5', 'cols' => '80', 'value' => set_value('terms_and_conditions', $terms_and_conditions) ) )?>
</p>
<?php echo form_submit('submitForm', 'Submit', 'class="bt-update"');
      echo form_close(); ?>
</div>