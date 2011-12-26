<?php if($public_invite) : ?>
	<?php // Change the css classes to suit your needs    

	$attributes = array('class' => '', 'id' => '');
	echo form_open('invite/invite_share/'.$app_install_id, $attributes); ?>
	<p>
		
	        <?php echo form_error('invite_message'); ?>
	        
	        <?php // Change the values/css classes to suit your needs ?>
	        <br /><input type="text" id="invite_message" name="invite_message" class="invite-message" value="<?php echo set_value('invite_message', $invite_message); ?>" /> 
	                   
		<label for="invite_message">Invite message</label>
	</p> 
	<p>
		
	        <?php echo form_error('invite_link'); ?>
	        
	        <?php // Change the values/css classes to suit your needs ?>
	        <br /><input type="text" id="invite_link" name="invite_link" class="invite-message" value="<?php echo set_value('invite_link', $invite_link); ?>" /> 
	                   
		<label for="invite_link">Invite link</label>
	</p> 

	<p>
	        <?php echo form_submit( 'submit', 'Submit'); ?>
	</p>

	<?php echo form_close(); ?>
<?php else : ?>
<p>Invite link : <input type="text" id="invite_message" name="invite_message" class="invite-message" value="<?php echo set_value('invite_message', $invite_message); ?>" /></p>
Send private invite via : facebook message, email
<?php endif;?>