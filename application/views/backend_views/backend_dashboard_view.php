<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend Dashboard</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Backend Dashboard</h1>
<?=anchor('backend/add_new_app/', 'Add New Apps', 'title="add new app to platform"');?>
 | <?=anchor('/', 'Back to home', 'title="go back to home"');?>
<h1>Apps</h1>
<ul>
<?php
	foreach ($app_list as $app) {
		echo "<li><b>" . $app['app_name'] . "</b> " . anchor('backend/edit_app/'.$app['app_id'], 'Edit', 'title="edit this app information"');
		echo "<br /> description: ".$app['app_description'];
		echo "<br /> app id: ".$app['app_id'];
		echo "<br /> app secret key: ".$app['app_secret_key']."</li>";
	}
?>
</ul>

</body>
</html>