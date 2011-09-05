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
			<?=anchor('backend/users/', 'Manage Users', 'title="Manage Users"');?>
		</li>
		<li>
			<?=anchor('backend/packages/', 'Manage Packages', 'title="Manage Packages"');?>
		</li>
		<li>
			<?=anchor('backend/achievements/', 'Manage Achievements', 'title="Manage Achievements"');?>
		</li>
	</ul>
	<?=anchor('backend/logout/', 'Logout', 'title="Logout backend"');?>
</body>
</html>