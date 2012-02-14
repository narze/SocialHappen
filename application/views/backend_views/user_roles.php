<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | User Roles</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
	<h1>Packages</h1>
	<?=anchor('backend/add_new_user_role/', 'Add New User Role', 'title="add new user role"');?>
 	| <?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
	<h1>User Roles</h1>
	<ul>
		<?php foreach($user_roles as $user_role) : ?>
			<li>
				<?=anchor('backend/user_role/'.$user_role['user_role_id'], $user_role['user_role_name'], 'title="'.$user_role['user_role_name'].'"');?>
			</li>
		<?php endforeach; ?>
	</ul>
</body>
</html>