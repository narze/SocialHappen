	<div id="reward">
		<h2><span>Reward List Setting</span></h2>

		<?php echo form_open("settings/page_reward/view/{$page_id}", array('class' => 'reward')); 
		if($updated) echo '<div class="notice success">Updated</div>'; ?>
		<h3>Terms and Conditions</h3>
		<?php echo form_error('terms_and_conditions'); ?>
		<br />
		<?php echo form_textarea( array( 'name' => 'terms_and_conditions', 'value' => set_value('terms_and_conditions', $terms_and_conditions) ) )?>

		<?php echo form_submit('submitForm', 'Submit', 'class="bt-update"');
	    echo form_close(); ?>

		<div class="tab-head">
			Sort by :
			<a class="tab active">Date Added</a>
			<a class="tab">Value</a>
			<a class="tab">Status</a>
			<a class="tab">Point</a>
			<a class="add-reward-item btn green fr"><span>+ Add new reward</span></a>
		</div>
		<div class="reward-item-list">
	    <?php if(issetor($reward_items)) :
	    	foreach($reward_items as $reward_item) : 
	    		$this->load->view('settings/page_apps/reward_item', array('reward_item'=>$reward_item));?>
	    	
		<?php endforeach;
			else :
				echo 'u have no reward now';
			endif;
	      ?>
	    </div>
	</div>