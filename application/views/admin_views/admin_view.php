<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Admin Dashboard</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body> 
<h1>Admin Dashboard</h1>
<?=anchor('company/create_new_company', 'Create new company profile', 'title="create your new company profile"');?>
 | <?=anchor('/', 'Back to home', 'title="go back to home"');?>
<h1>Company List</h1>
<ul>
<?php
	foreach ($company_list as $company) {
		echo "<li><b>" . $company->company_name . "</b> " . anchor('admin/dashboard/'.$company->company_id, 'Dashboard', 'title="see company\'s dashboard"');
		echo "</li>";
	}
?>
</ul>
</body>
</html>