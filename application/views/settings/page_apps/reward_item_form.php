<div class="reward-item">
<?php // Change the css classes to suit your needs    

		$attributes = array('class' => '', 'id' => '');
		if(issetor($update)){
			echo form_open(base_url().'/settings/page_reward/update_item/'.$page_id, $attributes);
			echo '<input type="hidden" name="reward_item_id" value="'.set_value('reward_item_id', $reward_item_id).'" />';
		} else {
			echo form_open(base_url().'/settings/page_reward/add_item/'.$page_id, $attributes);
		} ?>

		<p>
		        <label for="name">Name <span class="required">*</span></label>
		        <?php echo form_error('name'); ?>
		        <br /><input class="name" type="text" name="name" maxlength="255" value="<?php echo set_value('name', issetor($reward_item['name'])); ?>"  />
		</p>

		<p>
		        <label for="start_date">Start date <span class="required">*</span></label>
		        <?php echo form_error('start_date'); ?>
		        <br /><input class="start-date" type="text" name="start_date" maxlength="20" value="<?php echo set_value('start_date', issetor($reward_item['start_date'])); ?>"  />
		</p>

		<p>
		        <label for="end_date">End date <span class="required">*</span></label>
		        <?php echo form_error('end_date'); ?>
		        <br /><input class="end-date" type="text" name="end_date" maxlength="20" value="<?php echo set_value('end_date', issetor($reward_item['end_date'])); ?>"  />
		</p>

		<p>
		        <label for="point">Point <span class="required">*</span></label>
		        <?php echo form_error('point'); ?>
		        <br /><input id="point" type="text" name="point" maxlength="10" value="<?php echo set_value('point', issetor($reward_item['redeem']['point'])); ?>"  />
		</p>

		<p>
		        <label for="amount">Amount <span class="required">*</span></label>
		        <?php echo form_error('amount'); ?>
		        <br /><input id="amount" type="text" name="amount" maxlength="10" value="<?php echo set_value('amount', issetor($reward_item['redeem']['amount'])); ?>"  />
		</p>

		<p>
		        <label for="status">Status <span class="required">*</span></label>
		        <?php echo form_error('status'); ?>
		        
		        <?php // Change the values in this array to populate your dropdown as required ?>
		        <?php $options = array(
		                                                  ''  => 'Select',
		                                                  'draft'    => 'Draft',
		                                                  'published'    => 'Published'
		                                                ); ?>

		        <br /><?php echo form_dropdown('status', $options, set_value('status', issetor($reward_item['status'])))?>
		</p>                                             
		<!--                       
		<p>
		        <label for="type">Type <span class="required">*</span></label>
		        <?php echo form_error('type'); ?>
		        
		        <?php // Change the values in this array to populate your dropdown as required ?>
		        <?php $options = array(
		                                                  ''  => 'Select',
		                                                  'redeem'    => 'Redeem',
		                                                  'top_score'    => 'Top score',
		                                                  'random'    => 'Random',
		                                                ); ?>

		        <br /><?php echo form_dropdown('type', $options, set_value('type'))?>
		</p>                                             
		-->                   

		<p>
		        <?php echo form_submit( 'submit', 'Submit'); ?>
		</p>

		<?php echo form_close(); ?>
</div>