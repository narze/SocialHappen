<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>SH - Invite</title> 	

	<?php echo link_tag('assets/css/common/jquery.facebook.multifriend.select.css'); ?>
	<?php echo link_tag('assets/css/common/jquery.facebook.multifriend.select-list.css'); ?>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" language="javascript"></script>
	<script type="text/javascript">
		var base_url = '<?php echo base_url();?>';
		var facebook_app_id = '<?php echo $facebook_app_id; ?>';
		var app_install_id = '<?php echo $app_install_id; ?>';
		var campaign_id = '<?php echo $campaign_id; ?>';
		var channel_url = '<?php echo base_url();?>assets/channel/fb.html';
	</script>
	<script type="text/javascript" src="<?php echo base_url().'assets/js/invite/main.js';?>"></script>	
	<script src="https://connect.facebook.net/en_US/all.js#xfbml=1"></script>
	<script type="text/javascript" src="<?php echo base_url().'assets/js/common/jquery.facebook.multifriend.select.js';?>"></script>	
</head>
<body>
	<div id="fb-root"></div>
	<div id="invite-button">Invite</div>
	<?php // Change the css classes to suit your needs    

	$attributes = array('class' => 'invite-button', 'id' => '');
	echo form_open('invite/create_invite/'.$app_install_id, $attributes); ?>

	<p>
		
	        <?php echo form_error('private_invite'); ?>
	        
	        <?php // Change the values/css classes to suit your needs ?>
	        <br /><input type="checkbox" id="private_invite" name="private_invite" value="1" class="" <?php echo set_checkbox('private_invite', '1'); ?>> 
	                   
		<label for="private_invite">Private Invite</label>
	</p> 

	<div id="friend-list" style="display: none;">
		<div id="username"></div> 
		<div id="selected-friends"></div> 
		<div id="jfmfs-container"></div> 
	</div> 
	<div id="target_id-row" style="display: none;">
		<input type="text" name="target_id" id="target_id">
	</div>
	<p>
	        <label for="invite_message">Invite Message <span class="required">*</span></label>
		<?php echo form_error('invite_message'); ?>
		<br />
								
		<?php echo form_textarea( array( 'name' => 'invite_message', 'rows' => '5', 'cols' => '80', 'value' => set_value('invite_message', $invite_message) ) )?>
	</p>
	
	<input type="hidden" name="campaign_id" value="<?php echo $campaign_id;?>" />
	<input type="hidden" name="facebook_page_id" value="<?php echo $facebook_page_id;?>" />

	<p>
	        <?php echo form_submit( 'submit', 'Submit'); ?>
	</p>

	<?php echo form_close(); ?>
		
</body>
</html>