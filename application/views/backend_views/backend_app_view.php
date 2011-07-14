<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Manage Apps</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Manage Apps</h1>
<?=anchor('backend/add_new_app/', 'Add New Apps', 'title="add new app to platform"');?>
 | <?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Apps</h1>
<ul>
<?php
	echo "<li><b>" . anchor('backend/edit_platform/', 'Platform', 'title="edit this app information"') . "</b>
			<br />edit platform information</li>";
		
	foreach ($app_list as $app) {
		echo "<li><b>" . anchor('backend/edit_app/'.$app['app_id'], $app['app_name'], 'title="edit this app information"') . "</b>";
		echo "<br /> " . anchor('backend/list_audit_action/'.$app['app_id'], 'edit app\'s audit action', 'title="edit this app audit action"') . "</b>";
		echo "<br /> description: ".$app['app_description'];
		echo "<br /> app id: ".$app['app_id'];
		echo "<br /> app secret key: ".$app['app_secret_key']."</li>";
	}
?>
</ul>

</body>
</html>