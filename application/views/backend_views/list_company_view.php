<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Manage Companies</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Manage Companies</h1>
<?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Companies</h1>
<ul>
<?php
	foreach ($company_list as $company) {
		echo "<li>
		".anchor('backend/company_detail/'.$company['company_id'], $company['company_name'], 'title="view company detail"')."
		<br/>Detail: ".$company['company_detail']."
		</li>";
	}
?>
</ul>

</body>
</html>