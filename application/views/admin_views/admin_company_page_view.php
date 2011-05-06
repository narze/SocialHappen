<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Company Dashboard</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Company : <?php echo $company->company_name; ?> > Page : <?=anchor($page->facebook_page_url,$page->facebook_page_name, 'title="view page in Facebook"');?></h1>
<?=anchor('admin/dashboard/'.$company->company_id, 'Back to company dashboard', 'title="go back to company dashboard"');?>
<ul>
<?php
	foreach ($app_list as $app) {
		//echo "<li><b>" . $app->app_name . "</b> " . anchor($app->app_path.'/config', 'Configure', 'title="configure this app\'s setting"');
		echo "<li>" . anchor('admin/app/'.$company->company_id.'/'.$app->app_install_id.'/'.$facebook_page_id, '<b>'.$app->app_name.'</b>', 'title="see detail"') . " " . anchor($app->translated_app_config_url, 'Configure', 'title="configure this app\'s setting"');
		if($app->app_install_available){
			echo " | " . anchor('admin/deactivate_app/'.$company->company_id.'/'.$app->app_install_id, 'Deactivate', 'title="deactivate this app"');
		}else{
			echo " | " . anchor('admin/activate_app/'.$company->company_id.'/'.$app->app_install_id, 'Activate', 'title="activate this app"');
		}
		
		echo "<br /> - ".$app->app_description."</li>";
	}
?>
</ul>

</body>
</html>