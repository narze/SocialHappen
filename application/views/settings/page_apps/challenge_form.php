<div class="challenge">
	<?php if(issetor($update)){
			echo form_open(base_url().'settings/page_challenge/update/'.$page_id);
			echo '<input type="hidden" name="challenge_id" value="'.set_value('challenge_id', $challenge_id).'" />';
		} else {
			echo form_open(base_url().'settings/page_challenge/add/'.$page_id);
		}
	?>	
		Time
		<input class="start-date<?php echo form_error('start_date') ? ' form-error': ''; ?>" 
			type="text" name="start_date" maxlength="20" 
			value="<?php echo set_value('start_date', issetor($challenge['start_date'])); ?>" 
			/>
			to
		<input class="end-date<?php echo form_error('end_date') ? ' form-error': ''; ?>" 
			type="text" name="end_date" maxlength="20" 
			value="<?php echo set_value('end_date', issetor($challenge['end_date'])); ?>" 
			/><br />
		Detail :<br />
		Name
		<input type="text" name="detail[name]" 
			value="<?php 
				echo set_value('detail[name]',
					issetor($challenge['detail']['name'])); 
			?>" />
		Description
		<input type="text" name="detail[description]" 
			value="<?php 
				echo set_value('detail[description]',
					issetor($challenge['detail']['description'])); 
			?>" />
		Image
		<input type="text" name="detail[image]" 
			value="<?php 
				echo set_value('detail[image]',
					issetor($challenge['detail']['image'])); 
			?>" /><br />
		Criteria 1<br />
		Name
		<input type="text" name="criteria[0][name]" 
			value="<?php 
				echo set_value('criteria[0][name]',
					issetor($challenge['criteria'][0]['name'])); 
			?>" />
		Query : app_id
		<input type="text" name="criteria[0][query][app_id]" 
			value="<?php 
				echo set_value('criteria[0][query][app_id]',
					issetor($challenge['criteria'][0]['query']['app_id'])); 
			?>" />
		Query : action_id
		<input type="text" name="criteria[0][query][action_id]" 
			value="<?php 
				echo set_value('criteria[0][query][action_id]',
					issetor($challenge['criteria'][0]['query']['action_id'])); 
			?>" />
		Count
		<input type="text" name="criteria[0][count]" 
			value="<?php 
				echo set_value('criteria[0][count]',
					issetor($challenge['criteria'][0]['count'])); 
			?>" /><br />
		Criteria 2<br />
		Name
		<input type="text" name="criteria[1][name]" 
			value="<?php 
				echo set_value('criteria[1][name]',
					issetor($challenge['criteria'][1]['name'])); 
			?>" />
		Query : app_id
		<input type="text" name="criteria[1][query][app_id]" 
			value="<?php 
				echo set_value('criteria[1][query][app_id]',
					issetor($challenge['criteria'][1]['query']['app_id'])); 
			?>" />
		Query : action_id
		<input type="text" name="criteria[1][query][action_id]" 
			value="<?php 
				echo set_value('criteria[1][query][action_id]',
					issetor($challenge['criteria'][1]['query']['action_id'])); 
			?>" />
		Count
		<input type="text" name="criteria[1][count]" 
			value="<?php 
				echo set_value('criteria[1][count]',
					issetor($challenge['criteria'][1]['count'])); 
			?>" />
		<button>Add more criteria</button>
	<div class="section">
		<?php echo form_submit(array('name'=>'submit-form','value'=>'Submit')); ?>
	</div>
	<?php echo form_close(); ?>

</div>