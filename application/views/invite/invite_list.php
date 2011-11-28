<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>SH - Invite</title> 	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js" language="javascript"></script>
	<script type="text/javascript">
		var base_url = '<?php echo base_url();?>';
	</script>
	<script type="text/javascript" src="<?php echo base_url().'assets/js/invite/invite_list.js';?>"></script>
<body>
</head>
<body>
	<div id="report-button">Report</div>
	<div id="invite-report">
		<form id="report-form">
			<div>Campaign ID</div><div><input type="text" name="campaign_id" id="campaign_id"></div>
			<div>App Install ID</div><div><input type="text" name="app_install_id" id="app_install_id"></div>
			<div>Facebook Page ID</div><div><input type="text" name="facebook_page_id" id="facebook_page_id"></div>
			<div>User Facebook ID</div><div><input type="text" name="user_facebook_id" id="user_facebook_id"></div>
			<div style="display:none" id="invite-list-div">
				<div>Invite List</div>
				<div id="invite-list">No invite item matches criteria</div>
			</div>
			<div><input type="button" name="submit" id="report-submit" value="Get Report"></div>
		</form>
	</div>
	
</body>
</html>