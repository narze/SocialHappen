<div id="new-challenge-form">
	<?php if(issetor($success)) :?>
		<p>Updated</p>
	<?php endif; ?>
	<?php if(issetor($update)){
			echo '<h1>Update Challenge</h1>';
			echo form_open(base_url().'settings/page_challenge/update/'.$page_id,'class="new-challenge-form"');
			echo '<input type="hidden" name="challenge_id" value="'.set_value('challenge_id', $challenge_id).'" />';
		} else {
			echo '<h1>Add Challenge</h1>';
			echo form_open(base_url().'settings/page_challenge/add/'.$page_id,'class="new-challenge-form"');
		}
	?>	
		<ul>
			<li>
		Time
		<input class="start-date<?php echo form_error('start_date') ? ' form-error': ''; ?>" 
			type="text" name="start_date" maxlength="20" 
			value="<?php echo set_value('start_date', issetor($challenge['start_date'])); ?>" 
			/>
			to
		</li>
		<li>
		<input class="end-date<?php echo form_error('end_date') ? ' form-error': ''; ?>" 
			type="text" name="end_date" maxlength="20" 
			value="<?php echo set_value('end_date', issetor($challenge['end_date'])); ?>" 
			/>
			</li>
			<li>Detail :</li>
			<li>Name

		<input type="text" name="detail[name]" 
			value="<?php 
				echo set_value('detail[name]',
					issetor($challenge['detail']['name'])); 
			?>" /></li>
			<li>
		Description
		<input type="text" name="detail[description]" 
			value="<?php 
				echo set_value('detail[description]',
					issetor($challenge['detail']['description'])); 
			?>" />
			</li>
		<li>Image
		<input type="text" name="detail[image]" 
			value="<?php 
				echo set_value('detail[image]',
					issetor($challenge['detail']['image'])); 
			?>" /></li>

		<ul class="criteria_list">
			<?php 
			if(isset($challenge['criteria'])) {
				foreach($challenge['criteria'] as $nth => $criteria) : ?>
				<li class="criteria" data-nth="<?php echo $nth;?>">
					Criteria 1<br />
					Name
					<input class="name" type="text" name="criteria[<?php echo $nth;?>][name]" 
						value="<?php 
							echo set_value('criteria['.$nth.'][name]',
								$criteria['name']); 
						?>" /><br />
					Query : page_id
					<input class="page_id" type="text" name="criteria[<?php echo $nth;?>][query][page_id]" 
						value="<?php 
							echo set_value('criteria['.$nth.'][query][page_id]',
								$criteria['query']['page_id']); 
						?>" /><br />
					Query : app_id
					<input class="app_id" type="text" name="criteria[<?php echo $nth;?>][query][app_id]" 
						value="<?php 
							echo set_value('criteria['.$nth.'][query][app_id]',
								$criteria['query']['app_id']); 
						?>" /><br />
					Query : action_id
					<input class="action_id" type="text" name="criteria[<?php echo $nth;?>][query][action_id]" 
						value="<?php 
							echo set_value('criteria['.$nth.'][query][action_id]',
								$criteria['query']['action_id']); 
						?>" /><br />
					Count
					<input class="count" type="text" name="criteria[<?php echo $nth;?>][count]" 
						value="<?php 
							echo set_value('criteria['.$nth.'][count]',
								$criteria['count']); 
						?>" /><br />
					<p><a class="remove-criteria">Remove this criteria</a></p>
				</li>
			<?php endforeach; ?>
				<div class="criteria-template" style="display:none;" data-nth="">
					Criteria 1<br />
					Name
					<input class="name" type="text" /><br />
					Query : page_id
					<input class="page_id" type="text" /><br />
					Query : app_id
					<input class="app_id" type="text" /><br />
					Query : action_id
					<input class="action_id" type="text" /><br />
					Count
					<input class="count" type="text" /><br />
					<p><a class="remove-criteria">Remove this criteria</a></p>
				</div>
		</ul>
		<hr />
		Add criteria <br />
		Name <input id="name" type="text" />
		<div id="select_page"><?php 
			array_unshift($company_pages, 'Select Page');
			echo form_dropdown('select_page', $company_pages);
		?></div>
		<div id="select_app"> <?php
			echo form_dropdown('select_app', array(''=>'Select App'));
		?></div>
		<div id="select_action"> <?php
			echo form_dropdown('select_action', array(''=>'Select Action'));
		?></div>
		Count <input id="count" type="text"
		value="<?php 
			echo set_value('criteria[0][count]',
				issetor($challenge['criteria'][0]['count'])); 
		?>" />

		<p><a class="add-criteria">Add Criteria</a></p>
	<div class="section">
		<?php echo form_submit(array('name'=>'submit-form','value'=>'Submit')); ?>
	</div>
	<?php echo form_close(); ?>
	<?php echo anchor('settings/page_challenge/'.$page_id,'Back'); ?>
</div>