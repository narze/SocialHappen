<div class="reward-item">
<?php // Change the css classes to suit your needs    

		$attributes = array('class' => '', 'id' => '');
		if(issetor($update)){
			echo form_open(base_url().'/settings/page_reward/update_item/'.$page_id, $attributes);
			echo '<input type="hidden" name="reward_item_id" value="'.set_value('reward_item_id', $reward_item_id).'" />';
		} else {
			echo form_open(base_url().'/settings/page_reward/add_item/'.$page_id, $attributes);
		} ?>
		<div class="section">
			<div class="item-image" style="background-image:url(<?php echo issetor($reward_item['image']); ?>);">
				<input class="image" type="text" name="image" maxlength="255" value="<?php echo set_value('image', issetor($reward_item['image'])); ?>"  />
			</div>
			<ul class="item-info">
				<li>
					<label for="amount">Quanity: <span class="required">*</span></label>
					<input id="amount" type="text" name="amount" maxlength="10" value="<?php echo set_value('amount', issetor($reward_item['redeem']['amount'])); ?>" <?php echo form_error('amount') ? 'class="form-error"': ''; ?> />
				</li>
				<li>
					<label for="value">Value: <span class="required">*</span></label>
					<input id="value" type="text" name="value" maxlength="10" value="<?php echo set_value('value', issetor($reward_item['redeem']['value'])); ?>" <?php echo form_error('value') ? 'class="form-error"': ''; ?> />
				</li>
				<li>
					<label for="point">Point: <span class="required">*</span></label>
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
				<label for="desc">Reward Description: <span class="required">*</span></label>
				<?php echo form_textarea( array( 'name' => 'desc', 'value' => set_value('desc', issetor($reward_item['desc'])), 'style'=>'width:309px' ) )?>
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