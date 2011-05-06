<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Application Directory</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Application Directory : Install to <?php echo $company->company_name; ?></h1>
<?=anchor('admin/dashboard/'.$company_id, 'Back to dashboard', 'title="go back to dashboard"');?>
<ul>
<?php
	foreach ($app_list as $app) {
		//echo "<li><b>" . $app->app_name . "</b> " . anchor('install/install_new_app/'.$company_id.'/'.$app->app_id, 'Install', 'title="install this app"');
		echo "<li><b>" . $app->app_name . "</b> " . anchor('install/install_new_app/'.$company_id.'/'.$app->app_id, 'Install', 'title="install this app"');
		echo "<br /> - ".$app->app_description."</li>";
	}
?>
</ul>
</body>
</html>