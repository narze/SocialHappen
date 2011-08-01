<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Company</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Company : <?php echo $company['company_name']; ?></h1>
<?=anchor('backend/company', 'Back to company', 'title="go back to company"');?> | <?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Information</h1>
<p><b>Detail:</b> <?php echo $company['company_detail'];?></p>
<p><b>Address:</b> <?php echo $company['company_address'];?></p>
<p><b>Email:</b> <?php echo $company['company_email'];?></p>
<p><b>Telephone:</b> <?php echo $company['company_telephone'];?></p>
<p><b>Register Date:</b> <?php echo $company['company_register_date'];?></p>
<p><b>Website:</b> <a href="<?php echo $company['company_website'];?>"><?php echo $company['company_website'];?></a></p>
<p><img src="<?php echo $company['company_image'];?>" /></p>
<h1>Pages</h1>
<ul>
	<?php 
		foreach($page_list as $page){
			//var_dump($page);
			echo '<li>'.anchor('backend/page/'.$page['page_id'], $page['page_name'], 'title="view page detail"').'</li>';
		}
	?>
</ul>

<h1>Apps</h1>
<ul>
	<?php 
		foreach($app_list as $app){
			//var_dump($app);
			//echo '<li>'.anchor('backend/page/'.$page['page_id'], $page['page_name'], 'title="view page detail"').'</li>';
			echo '<li>'.anchor('backend/app_install/'.$app['app_install_id'], $app['app_name'], 'title="view app installed detail"').'
			<br/>Status: '.$app['app_install_status_name'].'
			
			</li>';
		}
	?>
</ul>

<h1>Activities</h1>
<ul>
	<?php 
		foreach($activity_list as $activity){
			echo '<li>'.$activity['message'].'</li>';
		}
	?>
</ul>
</body>
</html>