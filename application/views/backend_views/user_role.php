<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | User Roles</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
	<h1>User Roles</h1>
	<?=anchor('backend/add_new_user_role/', 'Add New User Role', 'title="add new user role"');?>
 	| <?=anchor('backend/user_roles', 'Back to user roles', 'title="go back to user roles"');?>
 	| <?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
	<h1>User Role</h1>
	<ul>
		<?php foreach($user_role as $key => $value) : ?>
			<li>
				<?php echo $key.' -> '.$value;?>
			</li>
		<?php endforeach; ?>
	</ul>
</body>
</html>