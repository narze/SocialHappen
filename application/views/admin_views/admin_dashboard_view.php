<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Company Dashboard</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Company Dashboard : <?php echo $company->company_name; ?></h1>
<?=anchor('install/apps/'.$company_id, 'Install Apps', 'title="select and install app to your company"');?>
 | <?=anchor('company/edit_company_profile/'.$company_id, 'Edit Company', 'title="edit company data"');?>
 | <?=anchor('admin', 'Back to admin dashboard', 'title="go back to admin dashboard"');?>
<h1>Company's Apps</h1>
<ul>
<?php
	foreach ($app_list as $app) {
		//echo "<li><b>" . $app->app_name . "</b> " . anchor($app->app_path.'/config', 'Configure', 'title="configure this app\'s setting"');
		echo "<li>" . anchor('admin/app/'.$company->company_id.'/'.$app->app_install_id, '<b>'.$app->app_name.'</b>', 'title="see detail"') . " " 
		. anchor($app->translated_app_url, 'Go to app', 'title=Go to app') . " | "
		. anchor($app->translated_app_config_url, 'Configure', 'title="configure this app\'s setting"');
		if($app->app_install_status){
			echo " | " . anchor('admin/deactivate_app/'.$company_id.'/'.$app->app_install_id, 'Deactivate', 'title="deactivate this app"');
		}else{
			echo " | " . anchor('admin/activate_app/'.$company_id.'/'.$app->app_install_id, 'Activate', 'title="activate this app"');
		}
		
		echo "<br /> - ".$app->app_description."</li>";
	}
?>
</ul>
<h1>Company's Pages</h1>
<ul>
<?php
 
foreach($page_list as $page){
	echo '<li><b>'.$page->facebook_page_name.'</b> '
	.anchor("admin/company_page/$company_id/$page->facebook_page_id", 'View page apps').' | '
	.anchor($page->facebook_page_url,'Go to page', 'title="go to page"').'</li>';
	
}
?>
</ul>

</body>
</html>