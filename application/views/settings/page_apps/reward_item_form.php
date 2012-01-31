<div class="reward-item">
<?php
		$attributes = array('class' => '', 'id' => '');
		if(issetor($update)){
			echo form_open(base_url().'/settings/page_reward/update_item/'.$page_id, $attributes);
			echo '<input type="hidden" name="reward_item_id" value="'.set_value('reward_item_id', $reward_item_id).'" />';
		} else {
			echo form_open(base_url().'/settings/page_reward/add_item/'.$page_id, $attributes);
		}

		$error = array();
		if(form_error('image')) $error[] = form_error('image');
		if(form_error('amount')) $error[] = form_error('amount');
		if(form_error('value')) $error[] = form_error('value');
		if(form_error('point')) $error[] = form_error('point');
		if(form_error('name')) $error[] = form_error('name');
		if(form_error('description')) $error[] = form_error('description');
		if(form_error('start_date')) $error[] = form_error('start_date');
		if(form_error('end_date')) $error[] = form_error('end_date');
		if(form_error('status')) $error[] = form_error('status');
		if(count($error)>0) { ?>
		<div class="notice error">
			<ul>
			<?php foreach($error as $msg) {echo $msg;} ?>
			</ul>
		</div><?php
		} ?>

		<div class="section first">
			<div class="item-image<?php echo form_error('image') ? ' error' : ''; ?>" style="background-image:url(<?php echo isset($reward_item['image']) && $reward_item['image'] ? $reward_item['image'] : base_url().'assets/images/default/reward.png'; ?>);">
				<input class="image" type="text" name="image" maxlength="255" value="<?php echo set_value('image', issetor($reward_item['image'])); ?>"  />
			</div>
			<ul class="item-info">
				<li>
					<label for="amount" class="green" >Quanity: <span class="required">*</span></label>
					<input id="amount" type="text" name="amount" maxlength="10" value="<?php echo set_value('amount', issetor($reward_item['redeem']['amount'])); ?>" <?php echo form_error('amount') ? 'class="form-error"': ''; ?> />
				</li>
				<li>
					<label for="value" class="green" >Value: <span class="required">*</span></label>
					<input id="value" type="text" name="value" maxlength="10" value="<?php echo set_value('value', issetor($reward_item['value'])); ?>" <?php echo form_error('value') ? 'class="form-error"': ''; ?> />
				</li>
				<li>
					<label for="point" class="green" >Point: <span class="required">*</span></label>
					<input id="point" type="text" name="point" maxlength="10" value="<?php echo set_value('point', issetor($reward_item['redeem']['point'])); ?>" <?php echo form_error('point') ? 'class="form-error"': ''; ?> />
				</li>
			</ul>
		</div>
		<div class="section">
			<div class="line">
				<label for="name">Reward Name: <span class="required">*</span></label>
				<input class="name bold<?php echo form_error('name') ? ' form-error': ''; ?>" type="text" name="name" maxlength="255" value="<?php echo set_value('name', issetor($reward_item['name'])); ?>" style="width:309px" />
			</div>
			<div class="line">
				<label for="description">Reward Description: <span class="required">*</span></label>
				<?php echo form_textarea( array( 'name' => 'description', 'value' => set_value('description', issetor($reward_item['description'])), 'style'=>'width:309px' ) )?>
			</div>
		</div>
		<div class="section">
			<label>Duration: <span class="required">*</span></label>
			<input class="start-date ta-center<?php echo form_error('start_date') ? ' form-error': ''; ?>" type="text" name="start_date" maxlength="20" value="<?php echo set_value('start_date', issetor($reward_item['start_date'])); ?>" style="width:135px; margin-right:5px" />
			to
			<input class="end-date ta-center<?php echo form_error('end_date') ? ' form-error': ''; ?>" type="text" name="end_date" maxlength="20" value="<?php echo set_value('end_date', issetor($reward_item['end_date'])); ?>" style="width:135px; margin-left:5px" />
		</div>
		<div class="section">
			<label for="status">Status <span class="required">*</span></label>
			<?php $class = form_error('status') ? 'class="form-error"' : ''; ?>
			<?php $options = array(
				'' => 'Select',
				'draft' => 'Draft',
				'published' => 'Published'
			); ?>
			<?php echo form_dropdown('status', $options, set_value('status', issetor($reward_item['status'])), $class)?>
		</div>
		<!--
		<div class="section">
			<label for="type">Type <span class="required">*</span></label>
			<?php echo form_error('type'); ?>
			<?php $options = array(
				'' => 'Select',
				'redeem' => 'Redeem',
				'top_score' => 'Top score',
				'random' => 'Random',
			); ?>
			<?php echo form_dropdown('type', $options, set_value('type'))?>
		</div>
		-->
		<div class="section">
			<?php echo form_submit(array('name'=>'submit','value'=>'Submit','style'=>'display:none')); ?>
			<a class="btn grey cancel"><span>Cancel</span></a>
			<a class="btn green fr save"><span>Save</span></a>
		</div>
		<?php echo form_close(); ?>
</div>