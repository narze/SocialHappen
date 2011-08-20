<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>App Dashboard</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>App Dashboard : <?php echo $app->app_name; ?></h1>
<?=anchor($app->translated_app_config_url, 'Configure', 'title="configure this app\'s setting"');?>
 | <?=anchor('admin/dashboard/'.$company_id, 'Company\'s Dashboard', 'title="see company\'s dashboard"');?>
 | <?=anchor('admin', 'Admin dashboard', 'title="go back to admin dashboard"');?>
 <?php if($facebook_page_id != NULL): ?>
 | <?=anchor('admin/company_page/'.$company_id.'/'.$facebook_page_id, 'Back to page dashboard', 'title="go back to page dashboard"');?>
 <?php endif;?>
<h1>App's Member</h1>
<ul>
<?php
	foreach ($user_list as $user) {
		//echo "<li><b>" . $app->app_name . "</b> " . anchor($app->app_path.'/config', 'Configure', 'title="configure this app\'s setting"');
		echo "<li>" . anchor('http://www.facebook.com/profile.php?id='.$user->user_facebook_id, '<b>'.$user->facebook_name.'</b>', 'title="see detail"') . " ";
		
		
		echo "<br /> - first use: ".$user->user_apps_register_date."";
		echo "<br /> - last seen: ".$user->user_apps_last_seen."</li>";
	}
?>
</ul>

<h1>Active Member</h1>
<ul>
<?php
	foreach ($active_user_list as $active_user) {
		echo "<li>Time: ".$active_user->job_time." Active User: ".$active_user->active_user."</li>";
	}
?>
</ul>
</body>
</html>