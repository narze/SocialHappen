<div id="new-challenge-form">
	<?php if(issetor($success)) :?>
		<div class="notice success">Updated</div>
	<?php endif; ?>


	<?php if(issetor($update)){
			echo '<h2><span>Update Challenge</span></h2>';
			echo form_open(base_url().'settings/page_challenge/update/'.$page_id,'class="new-challenge-form"');
			echo '<input type="hidden" name="challenge_id" value="'.set_value('challenge_id', $challenge_id).'" />';
		} else {
			echo '<h2><span>Add Challenge</span></h2>';
			echo form_open(base_url().'settings/page_challenge/add/'.$page_id,'class="new-challenge-form"');
		}
	?>

	<div class="challenge-preview">
		<div class="wrapper">
			<div class="challenge-preview-name">
				<input type="text" name="detail[name]"
					value="<?php
						echo set_value('detail[name]',
							issetor($challenge['detail']['name']));
					?>" placeholder="Challenge name"/>
			</div>
			<div class="challenge-preview-image">
				<i class="image">
					<input type="text" name="detail[image]"
					value="<?php
						echo set_value('detail[image]',
							issetor($challenge['detail']['image']));
					?>" placeholder="Image" />
				</i>
			</div>
			<div class="challenge-preview-desc">
				<textarea name="detail[description]" placeholder="Challenge description"><?php
					echo set_value('detail[description]', issetor($challenge['detail']['description']));
				?></textarea>
			</div>
			<div class="challenge-preview-reward">Rewards :
				<span></span>
			</div>
			<div class="challenge-preview-actions">
				<ol>
					<li>Add action</li>
				</ol>
			</div>
		</div>
		<div class="challenge-preview-qr-code">
			<?php if (isset($hash)) {
				echo '<img width="100px" height="100px" src="'.base_url('qr?path=c/'.$hash).'" />';
			};?>
		</div>
	</div>

	<ul class="challenge-properties-list">
		<li class="challenge-property active">
			<div class="challenge-property-name">Actions</div>
			<div class="challenge-property-box">
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
							Use app action<br />
							Query : page_id
							<input class="page_id" type="text" name="criteria[<?php echo $nth;?>][query][page_id]"
								value="<?php
									echo set_value('criteria['.$nth.'][query][page_id]',
										issetor($criteria['query']['page_id']));
								?>" /><br />
							Query : app_id
							<input class="app_id" type="text" name="criteria[<?php echo $nth;?>][query][app_id]"
								value="<?php
									echo set_value('criteria['.$nth.'][query][app_id]',
										issetor($criteria['query']['app_id']));
								?>" /><br />
							Query : action_id
							<input class="action_id" type="text" name="criteria[<?php echo $nth;?>][query][action_id]"
								value="<?php
									echo set_value('criteria['.$nth.'][query][action_id]',
										issetor($criteria['query']['action_id']));
								?>" /><br />
							Or use Platform action
							<input class="platform_action_id" type="text" name="criteria[<?php echo $nth;?>][query][platform_action_id]"
								value="<?php
									echo set_value('criteria['.$nth.'][query][platform_action_id]',
										issetor($criteria['query']['platform_action_id']));
								?>" /><br />
							<div class="platform-action-setting"></div>
							Count
							<input class="count" type="text" name="criteria[<?php echo $nth;?>][count]"
								value="<?php
									echo set_value('criteria['.$nth.'][count]',
										$criteria['count']);
								?>" /><br />

							<p><a class="remove-criteria">Remove this criteria</a></p>
						</li>
						<?php endforeach;
					} ?>
					<div class="criteria-template" style="display:none;" data-nth="">
						Name
						<input class="name" type="text" /><br />
						Query : page_id
						<input class="page_id" type="text" /><br />
						Query : app_id
						<input class="app_id" type="text" /><br />
						Query : action_id
						<input class="action_id" type="text" /><br />
						Query : platform_action_id
						<input class="platform_action_id" type="text" /><br />
						Count
						<input class="count" type="text" /><br />
						<p><a class="remove-criteria">Remove this criteria</a></p>
					</div>
				</ul>

				<p class="mb20 bold">Add criteria </p>
				Name <input id="name" type="text" />
				<div id="select_page"><?php
						array_unshift($company_pages, 'Select Page');
						echo form_dropdown('select_page', $company_pages);
					?>
				</div>
				<div id="select_app"> <?php
						echo form_dropdown('select_app', array(''=>'Select App'));
					?>
				</div>
				<div id="select_action"> <?php
						echo form_dropdown('select_action', array(''=>'Select Action'));
					?>
				</div>
				Or
				<div id="select_platform_action"> <?php
						echo form_dropdown('select_platform_action', $platform_actions);
					?>
				</div>
				<div class="platform-action-setting"></div>
				Count <input id="count" type="text"	value="<?php
						echo set_value('criteria[0][count]',
							issetor($challenge['criteria'][0]['count']));
					?>" />

				<p class="mt20"><a class="btn green add-criteria"><span>Add Criteria</span></a></p>
			</div>
		</li>

		<li class="challenge-property">
			<div class="challenge-property-name">Rewards</div>
			<div class="challenge-property-box">
				<p class="mt10 mb10 bold">Add reward</p>
				<ul class="item-info">
					<li>
						<label for="name" >Reward Name: </label>
						<input type="text" name="name" maxlength="255" value="">
					</li>
					<li>
						<label for="amount" >Quantity: </label>
						<input id="amount" type="text" name="amount" maxlength="10" value="">
					</li>
					<li>
						<label for="value" >Value (USD): </label>
						<input id="value" type="text" name="value" maxlength="10" value="">

					</li>
					<li>
						<label for="point" >Required Point: </label>
						<input id="point" type="text" name="point" maxlength="10" value="">
					</li>
				</ul>

				<p class="mt25 mb10 bold">Or select from existing rewards</p>
				<select name="select_reward">
					<option value="">Select Reward</option>
				</select>
				<p class="mt10"><a class="btn green add-criteria"><span>Add Reward</span></a></p>
			</div>
		</li>

		<li class="challenge-property">
			<div class="challenge-property-name">Duration</div>
			<div class="challenge-property-box">
				<div>
					<label for="">Time</label>
					<input class="start-date<?php echo form_error('start_date') ? ' form-error': ''; ?>"
						type="text" name="start_date" maxlength="20"
						value="<?php echo set_value('start_date', issetor($challenge['start_date'])); ?>"
					/>
				</div>
				<div>
					<label for="">to</label>
					<input class="end-date<?php echo form_error('end_date') ? ' form-error': ''; ?>"
						type="text" name="end_date" maxlength="20"
						value="<?php echo set_value('end_date', issetor($challenge['end_date'])); ?>"
						/>
				</div>
			</div>
		</li>

		<li class="challenge-property">
			<div class="challenge-property-name">Design</div>
			<div class="challenge-property-box">
				<p>Background color picker</p>
				<p>Text color picker</p>
			</div>
		</li>
	</ul>

	<div class="ta-right mt10">
		<button type="button" class="btn green fl"><span>Print</span></button>
		<?php echo anchor('settings/page_challenge/'.$page_id,'<span>Back</span>','class="btn grey"'); ?>
		<?php //echo form_submit(array('name'=>'submit-form','value'=>'Save')); ?>
		<button type="submit" class="btn green" name="submit-form"><span>Save</span></button>
	</div>
	<?php echo form_close(); ?>

</div>