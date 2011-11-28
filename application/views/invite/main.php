<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>SH - Invite</title> 	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" language="javascript"></script>
	<script type="text/javascript">
		var base_url = '<?php echo base_url();?>';
		var invite_url = '<?php echo $invite_url;?>';
	</script>
	<script type="text/javascript" src="<?php echo base_url().'assets/js/invite/main.js';?>"></script>	
</head>
<body>
	
	<div id="invite-button">Invite</div>
	<div id="invite-form">
		<form id="invite-form">
			<div>Campaign ID</div><div><input type="text" name="campaign_id" id="campaign_id"></div>
			<div>App Install ID</div><div><input type="text" name="app_install_id" id="app_install_id"></div>
			<div>Facebook Page ID</div><div><input type="text" name="facebook_page_id" id="facebook_page_id"></div>
			<div>User Facebook ID</div><div><input type="text" name="user_facebook_id" id="user_facebook_id"></div>
			<div>Invitation Type</div>
			<div><input type="radio" class="invite_type" name="invite_type" value="0" checked> Private</div>
			<div><input type="radio" class="invite_type" name="invite_type" value="1"> Public</div>
			<div id="target_id-row"><div>Target Facebook ID</div><div><input type="text" name="target_facebook_id" id="target_facebook_id"></div></div>
			<div>Message</div><div><textarea name="message" id="message"></textarea></div>
			<div style="display:none" id="invite-key-div">
				<div>Invite Key</div><div id="invite-key"></div>
			</div>
			<div style="display:none" id="invite-error-div">
				<div>Invite Key</div><div id="invite-error"></div>
			</div>
			<div><input type="button" name="submit" id="invite-submit" value="Invite"></div>
		</form>
	</div>
		
</body>
</html>