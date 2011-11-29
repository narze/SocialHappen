<div id="user_class">
      <?php echo form_open("settings/page_user_class/view/{$page_id}", array('class' => 'user_class')); 
      if($updated) echo '<p>Updated</p>'; ?>

      <!--<p>
	
	        <?php echo form_error('enable'); ?>
	        <br /><input type="checkbox" id="enable" name="enable" value="1" class="" <?php echo set_checkbox('enable', '1'); ?>> 
	                   
		<label for="enable">Enable</label>
	</p>-->

    <?php if(issetor($page_user_class)) :
    	foreach($page_user_class as $one_class) : 
    	$aid = $one_class['achievement_id'];?>
	    <p>
	    	<input type="hidden" name="aid[]" value="<?php echo $aid;?>" />

	        <label for="name[<?php echo $aid;?>]">Name <span class="required">*</span></label>
	        <?php echo form_error('name['.$aid.']'); ?>
	        <br /><input id="name[<?php echo $aid;?>]" type="text" name="name[<?php echo $aid;?>]" maxlength="50" value="<?php echo set_value('name['.$aid.']',$one_class['name']); ?>"  />

	        <label for="invite_accepted">Invite Accepted <span class="required">*</span></label>
	        <?php echo form_error('invite_accepted['.$aid.']'); ?>
	        <br /><input id="invite_accepted[<?php echo $aid;?>]" type="text" name="invite_accepted[<?php echo $aid;?>]" maxlength="10" value="<?php echo set_value('invite_accepted['.$aid.']',$one_class['invite_accepted']); ?>"  />
		</p>
	<?php endforeach;
		else :
			echo anchor('settings/page_user_class/add_default_classes/'.$page_id, 'Add page classes');
		endif;
      echo form_submit('submitForm', 'Submit', 'class="bt-update"');
      echo form_close(); ?>
</div>