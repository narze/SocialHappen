<div id="reward-terms-and-conditions">
	<h2><span>Reward List Setting</span></h2>
	<?php echo form_open("settings/page_reward/terms_and_conditions/{$page_id}", array('class' => '-terms-and-conditions')); 
	if($updated) echo '<div class="notice success">Updated</div>'; ?>

	<div class="terms-and-conditions">
		<h3 class="mt10">Terms and Conditions</h3>
		<?php echo form_error('terms_and_conditions'); ?>
		<br />
		<?php echo form_textarea( array( 'name' => 'terms_and_conditions', 'value' => set_value('terms_and_conditions', $terms_and_conditions) ) )?>

		<div class="mt20">
			<label for="status" class="tc-green6 bold mr5">Status: </label>
			<?php $class = form_error('status') ? 'class="form-error"' : ''; ?>
			<?php $options = array(
				'' => 'Select',
				'draft' => 'Draft',
				'published' => 'Published'
			); ?>
			<?php echo form_dropdown('status', $options, set_value('status', issetor($reward_item['status'])), $class)?>
			<?php echo form_submit('submitForm', 'Submit', 'style="display:none"'); ?>
			<a class="btn green fr save"><span>Save</span></a>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>