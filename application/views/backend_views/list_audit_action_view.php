<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Manage Audit Action</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Manage Audit Action : <?php echo $app_name; ?></h1>
<?=anchor('backend/add_audit_action/'.$app_id, 'Add New Audit Action', 'title="add new audt action to this app"');?>
 | <?=anchor('/backend/app/', 'Back to app', 'title="go back to app"');?> | <?=anchor('/backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Audit Actions</h1>
<ul>
<?php
	foreach ($audit_action_list as $audit_action) {
		
		$stat_app = $audit_action['stat_app']?'Yes':'No';
		$stat_page = $audit_action['stat_page']?'Yes':'No';
		$stat_campaign = $audit_action['stat_campaign']?'Yes':'No';
		
		echo "<li><p>Action ID: ".$audit_action['action_id'].' '
		.anchor('backend/edit_audit_action/'.$app_id.'/'.$audit_action['action_id'], 'edit', 'title="edit this audt action"')." | "
		.anchor('backend/delete_audit_action/'.$app_id.'/'.$audit_action['action_id'], 'delete', 'title="delete this audt action"')."
		<br/>Description: ".$audit_action['description']."
		<br/>Collect Stat for App: ".$stat_app."
		<br/>Collect Stat for Page: ".$stat_page."
		<br/>Collect Stat for Campaign: ".$stat_campaign."</p></li>";
	}
?>
</ul>

</body>
</html>