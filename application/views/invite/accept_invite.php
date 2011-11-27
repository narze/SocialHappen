<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>SH - Invite</title> 	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" language="javascript"></script>
	<script type="text/javascript">
		var base_url = '<?php echo base_url();?>';
	</script>
	<script type="text/javascript" src="<?php echo base_url().'assets/js/invite/accept_invite.js';?>"></script>
</head>
<body>

	<div>Invite Key</div>
	<div><input type="text" name="invite_key" id="invite_key" value="<?php echo $invite_key; ?>" readonly></div>
	<div id="target_id-div">
		<div>Target Facebook ID</div>
		<div><input type="text" name="target_facebook_id" id="target_facebook_id" value="<?php echo @$target_facebook_id; ?>"></div>
	</div>
	<div id="invite-result-block" style="display:none">
		<div>Invite Result</div>
		<div id="invite-result"></div>
	</div>
	<div><input type="button" id="invite-accept" value="Accept Invite"></div>

	
</body>
</html>