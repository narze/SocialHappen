<div id="reward">
      <?php echo form_open("settings/page_reward/view/{$page_id}", array('class' => 'reward')); 
      if($updated) echo '<p>Updated</p>'; ?>

  <p>
        <label for="terms_and_conditions">Terms and Conditions</label>
	<?php echo form_error('terms_and_conditions'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => 'terms_and_conditions', 'rows' => '5', 'cols' => '80', 'value' => set_value('terms_and_conditions', $terms_and_conditions) ) )?>
</p>

    <?php if(issetor($reward_items)) :
    	foreach($reward_items as $one_reward) : ?>
	    <p>
	    	<?php var_dump_pre($one_reward); ?>
		</p>
	<?php endforeach;
		else :
			echo 'u have no reward now';
		endif;
      echo form_submit('submitForm', 'Submit', 'class="bt-update"');
      echo form_close(); ?>
</div>