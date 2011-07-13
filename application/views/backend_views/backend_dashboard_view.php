<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Dashboard</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
	<h1>Backend Dashboard</h1>
	<ul>
		<li>
			<?=anchor('backend/app/', 'Manage Apps', 'title="Manage Apps"');?>
		</li>
		<li>
			<?=anchor('backend/company/', 'Manage Companies', 'title="Manage Companies"');?>
		</li>
		<li>
			<?=anchor('backend/user/', 'Manage Users', 'title="Manage Users"');?>
		</li>
	</ul>
</body>
</html>