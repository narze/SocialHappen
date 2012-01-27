<div id="reward">
      <?php echo form_open("settings/page_reward/view/{$page_id}", array('class' => 'reward')); 
      if($updated) echo '<p>Updated</p>'; ?>

  <p>
        <label for="terms_and_conditions">Terms and Conditions</label>
	<?php echo form_error('terms_and_conditions'); ?>
	<br />
							
	<?php echo form_textarea( array( 'name' => 'terms_and_conditions', 'rows' => '5', 'cols' => '80', 'value' => set_value('terms_and_conditions', $terms_and_conditions) ) )?>
</p>
<?php echo form_submit('submitForm', 'Submit', 'class="bt-update"');
      echo form_close(); ?>
	<p><a class="add-reward-item">Add reward item</a></p>
	<div class="reward-item-list"></div>
    <?php if(issetor($reward_items)) :
    	foreach($reward_items as $reward_item) : 
    		$this->load->view('settings/page_apps/reward_item', array('reward_item'=>$reward_item));?>
    	
	<?php endforeach;
		else :
			echo 'u have no reward now';
		endif;
      ?>
</div>