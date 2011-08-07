<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Backend | Users</title>
	<?php echo link_tag('css/style.css'); ?>
</head>
<body>
<h1>Users</h1>
<?=anchor('backend/dashboard', 'Back to dashboard', 'title="go back to dashboard"');?>
<h1>Users List</h1>
<p>total users: <?php echo $total_user; ?></p>
<p><?php echo $pagination; ?></p>
<ul>
	<?php 
		foreach($user_list as $user){
			//var_dump($campaign);
			echo '<li><b>'.anchor('backend/user/'.$user['user_id'], $user['user_first_name'].' '.$user['user_last_name'], 'title="view user detail"').'</b>
			<br/><b>User ID:</b> '. $user['user_id'].'
			</li>';
		}
	?>
	
</ul>
<p><?php echo $pagination; ?></p>
</body>
</html>