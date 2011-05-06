<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Company Information</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Company Profile</h1>
<?=anchor('backend', 'Back to backend', 'title="go back to backend"');?>
<ul>
<li>Company Name : <?php echo $company->company_name; ?></li>
<li>Company Address : <?php echo $company->company_address; ?></li>
<li>Company Email : <?php echo $company->company_email; ?></li>
<li>Company Telephone : <?php echo $company->company_telephone; ?></li>
<li>Company Reigster Date : <?php echo $company->company_register_date; ?></li>
</ul>
<h1>Company's Apps</h1>
<ul>
<?php
	foreach ($app_list as $app) {
		echo "<li><b>" . $app->app_name . "</b>";	
		echo "<br /> - ".$app->app_description."</li>";
	}
?>
</ul>
</body>
</html>